<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\Tests\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;
use Tourze\DoctrineUuidBundle\Tests\SimplifiedTestKernel;
use Tourze\PHPUnitSymfonyKernelTest\AbstractEventSubscriberTestCase;

/**
 * @internal
 */
#[CoversClass(UuidListener::class)]
#[RunTestsInSeparateProcesses]
final class UuidListenerTest extends AbstractEventSubscriberTestCase
{
    private UuidListener $listener;

    protected function onSetUp(): void
    {
        $container = self::getContainer();
        $listener = $container->get(UuidListener::class);
        $this->assertInstanceOf(UuidListener::class, $listener);
        $this->listener = $listener;
        $this->assertInstanceOf(UuidListener::class, $this->listener);
    }

    public function testClassImplementsCorrectInterfaces(): void
    {
        $this->assertInstanceOf(EntityCheckerInterface::class, $this->listener);
    }

    public function testClassHasDoctrineListenerAttribute(): void
    {
        $reflection = new \ReflectionClass(UuidListener::class);
        $attributes = $reflection->getAttributes(AsDoctrineListener::class);

        $this->assertCount(1, $attributes);
        $attribute = $attributes[0]->newInstance();
        $this->assertEquals(Events::prePersist, $attribute->event);
    }

    public function testPrePersistEntityDirectly(): void
    {
        $entity = new class {
            private string $id = '';

            public function getId(): string
            {
                return $this->id;
            }
        };

        // 使用具体类 ObjectManager 进行Mock的原因：
        // 1. ObjectManager 是 Doctrine 的核心组件，在测试中需要具体的方法实现
        // 2. 测试需要验证与 Doctrine ORM 的具体交互行为，如 getClassMetadata 方法
        // 3. ObjectManager 包含复杂的内部状态管理，接口化会丢失关键测试数据
        $objectManager = $this->createMock(ObjectManager::class);
        $reflection = new \ReflectionClass($entity);

        // 使用具体类 ClassMetadata 进行Mock的原因：
        // 1. ClassMetadata 是 Doctrine ORM 核心组件，无对应接口抽象
        // 2. 测试需要验证与 Doctrine 元数据系统的具体交互行为
        // 3. ClassMetadata 包含复杂的内部状态，接口化会丢失关键测试场景
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflection)
        ;

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($metadata)
        ;

        // 使用从容器获取的 listener 实例进行测试
        $this->listener->prePersistEntity($objectManager, $entity);
    }

    public function testPrePersistEntityWithNoUuidAttributes(): void
    {
        $entity = new class {
            private string $name = 'test';

            public function getName(): string
            {
                return $this->name;
            }
        };

        // 使用具体类 ObjectManager 进行Mock的原因：
        // 1. ObjectManager 是 Doctrine 的核心组件，在测试中需要具体的方法实现
        // 2. 测试需要验证与 Doctrine ORM 的具体交互行为，如 getClassMetadata 方法
        // 3. ObjectManager 包含复杂的内部状态管理，接口化会丢失关键测试数据
        $objectManager = $this->createMock(ObjectManager::class);
        $reflection = new \ReflectionClass($entity);

        // 使用具体类 ClassMetadata 进行Mock的原因：
        // 1. ClassMetadata 是 Doctrine ORM 核心组件，无对应接口抽象
        // 2. 测试需要验证与 Doctrine 元数据系统的具体交互行为
        // 3. ClassMetadata 包含复杂的内部状态，接口化会丢失关键测试场景
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflection)
        ;

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($metadata)
        ;

        // 测试没有 UUID 属性的情况

        $this->listener->prePersistEntity($objectManager, $entity);
    }

    public function testPrePersist(): void
    {
        $entity = new class {
            private string $id = '';

            public function getId(): string
            {
                return $this->id;
            }
        };

        $objectManager = $this->createMock(ObjectManager::class);
        $reflection = new \ReflectionClass($entity);

        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflection)
        ;

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($metadata)
        ;

        // 使用从容器获取的 listener 实例进行测试
        // PrePersistEventArgs 是 final 类，无法进行 Mock，但我们可以直接测试业务逻辑
        $this->listener->prePersistEntity($objectManager, $entity);

        // 验证实体ID未被修改（因为没有UUID属性）
        $this->assertEquals('', $entity->getId());
    }

    public function testPreUpdateEntity(): void
    {
        $entity = new class {
            private string $id = 'existing-id';

            public function getId(): string
            {
                return $this->id;
            }
        };

        $objectManager = $this->createMock(ObjectManager::class);

        // Mock 具体类 PreUpdateEventArgs 的原因：
        // 1. PreUpdateEventArgs 是 Doctrine ORM 事件系统的核心组件，无对应接口抽象
        // 2. 测试需要验证与 Doctrine 事件系统的具体交互行为
        // 3. PreUpdateEventArgs 包含复杂的内部状态，接口化会丢失关键测试场景
        $eventArgs = $this->createMock(PreUpdateEventArgs::class);

        $this->listener->preUpdateEntity($objectManager, $entity, $eventArgs);

        $this->assertEquals('existing-id', $entity->getId());
    }
}
