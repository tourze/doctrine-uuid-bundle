<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Uid\Uuid;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;
use Tourze\DoctrineUuidBundle\Tests\Integration\Entity\TestEntity;

class DoctrineUuidIntegrationTest extends KernelTestCase
{
    protected function setUp(): void
    {
        self::bootKernel();
        $this->createSchema();
    }

    protected function tearDown(): void
    {
        $this->dropSchema();
        self::ensureKernelShutdown();
        parent::tearDown();
    }

    protected static function getKernelClass(): string
    {
        return IntegrationTestKernel::class;
    }

    private function createSchema(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->createSchema($metadata);
    }

    private function dropSchema(): void
    {
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new \Doctrine\ORM\Tools\SchemaTool($em);
        $schemaTool->dropSchema($metadata);
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
        $loadedEntity = $em->getRepository(TestEntity::class)->findOneBy(['name' => 'Preset UUID Entity']);
        $this->assertInstanceOf(TestEntity::class, $loadedEntity);
        $this->assertEquals($presetUuid, $loadedEntity->getUuid());
    }
}
