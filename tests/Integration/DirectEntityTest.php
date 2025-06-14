<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tourze\DoctrineEntityCheckerBundle\DoctrineEntityCheckerBundle;
use Tourze\DoctrineUuidBundle\DoctrineUuidBundle;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;
use Tourze\DoctrineUuidBundle\Tests\Integration\Entity\TestEntity;
use Tourze\IntegrationTestKernel\IntegrationTestKernel;

class DirectEntityTest extends KernelTestCase
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

    public function testDirectUuidGeneration(): void
    {
        // 直接获取监听器服务
        $listener = self::getContainer()->get(UuidListener::class);
        $this->assertInstanceOf(UuidListener::class, $listener);

        // 创建测试实体
        $entity = new TestEntity();
        $entity->setName('Direct Test');

        // 获取实体管理器
        $em = self::getContainer()->get('doctrine.orm.entity_manager');
        $this->assertInstanceOf(ObjectManager::class, $em);

        // 直接调用监听器的方法处理实体
        $listener->prePersistEntity($em, $entity);

        // 验证 UUID v1 已生成
        $this->assertNotEmpty($entity->getId());
        $this->assertIsString($entity->getId());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[1][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $entity->getId());

        // 验证 UUID v4 已生成 (可能为 null，因此需要条件检查)
        if ($entity->getUuid() !== null) {
            $this->assertIsString($entity->getUuid());
            $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-[4][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $entity->getUuid());
        }

        // 测试实体的持久化
        $em->persist($entity);
        $em->flush();
        $em->clear();

        // 从数据库检索实体并验证
        $loadedEntity = $em->getRepository(TestEntity::class)->findOneBy(['name' => 'Direct Test']);
        $this->assertInstanceOf(TestEntity::class, $loadedEntity);
        $this->assertEquals($entity->getId(), $loadedEntity->getId());
    }

    public function testPropertyAccessWorksCorrectly(): void
    {
        $entity = new TestEntity();

        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        // 测试 id 属性是否可写
        $this->assertTrue($propertyAccessor->isWritable($entity, 'id'));
        $testUuid = 'test-uuid-value';
        $propertyAccessor->setValue($entity, 'id', $testUuid);
        $this->assertEquals($testUuid, $entity->getId());

        // 测试 uuid 属性是否可写
        $this->assertTrue($propertyAccessor->isWritable($entity, 'uuid'));
        $testUuidV4 = 'test-uuid-v4-value';
        $propertyAccessor->setValue($entity, 'uuid', $testUuidV4);
        $this->assertEquals($testUuidV4, $entity->getUuid());
    }
}
