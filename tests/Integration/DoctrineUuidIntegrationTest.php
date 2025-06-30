<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Tourze\DoctrineEntityCheckerBundle\DoctrineEntityCheckerBundle;
use Tourze\DoctrineUuidBundle\DoctrineUuidBundle;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;
use Tourze\DoctrineUuidBundle\Tests\Integration\Entity\TestEntity;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

class DoctrineUuidIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        // 数据库模式由通用内核自动创建
    }

    protected function tearDown(): void
    {
        self::ensureKernelShutdown();
        parent::tearDown();
    }

    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    protected static function createKernel(array $options = []): IntegrationTestKernel
    {
        $appendBundles = [
            FrameworkBundle::class => ['all' => true],
            DoctrineBundle::class => ['all' => true],
            DoctrineEntityCheckerBundle::class => ['all' => true],
            DoctrineUuidBundle::class => ['all' => true],
        ];
        
        $entityMappings = [
            'Tourze\DoctrineUuidBundle\Tests\Integration\Entity' => __DIR__ . '/Entity',
        ];

        return new IntegrationTestKernel(
            $options['environment'] ?? 'test',
            $options['debug'] ?? true,
            $appendBundles,
            $entityMappings
        );
    }

    public function testServiceWiring(): void
    {
        $container = self::getContainer();
        $this->assertTrue($container->has(UuidListener::class));
    }

    public function testPersistEntityWithUuid(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $entity = new TestEntity();
        $entity->setName('Test Entity');

        // 手动设置 UUID，确保测试的一致性
        $id = Uuid::v1()->toRfc4122();
        $entity->setId($id);

        $em->persist($entity);
        $em->flush();

        // 验证 ID 已设置并被持久化
        $this->assertEquals($id, $entity->getId());

        $em->clear();

        // 从数据库加载并验证
        /** @phpstan-ignore-next-line */
        $loadedEntity = $em->getRepository(TestEntity::class)->findOneBy(['name' => 'Test Entity']);
        $this->assertInstanceOf(TestEntity::class, $loadedEntity);
        $this->assertEquals($id, $loadedEntity->getId());
    }

    public function testPersistEntityWithPresetUuid(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);

        $entity = new TestEntity();
        $entity->setName('Preset UUID Entity');

        // 设置 ID 和 UUID
        $id = Uuid::v1()->toRfc4122();
        $entity->setId($id);

        $presetUuid = '123e4567-e89b-42d3-a456-556642440000';
        $entity->setUuid($presetUuid);

        $em->persist($entity);
        $em->flush();

        // 验证预设的 UUID 没有被覆盖
        $this->assertEquals($presetUuid, $entity->getUuid());

        $em->clear();

        // 从数据库加载并验证
        /** @phpstan-ignore-next-line */
        $loadedEntity = $em->getRepository(TestEntity::class)->findOneBy(['name' => 'Preset UUID Entity']);
        $this->assertInstanceOf(TestEntity::class, $loadedEntity);
        $this->assertEquals($presetUuid, $loadedEntity->getUuid());
    }
}
