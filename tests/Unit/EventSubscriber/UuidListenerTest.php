<?php

namespace Tourze\DoctrineUuidBundle\Tests\Unit\EventSubscriber;

use Doctrine\Bundle\DoctrineBundle\Attribute\AsDoctrineListener;
use Doctrine\ORM\Events;
use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Tourze\DoctrineEntityCheckerBundle\Checker\EntityCheckerInterface;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;

class UuidListenerTest extends TestCase
{
    private PropertyAccessor $propertyAccessor;
    private ?\PHPUnit\Framework\MockObject\MockObject $logger = null;
    private UuidListener $listener;

    protected function setUp(): void
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->listener = new UuidListener($this->propertyAccessor, $this->logger);
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

        $objectManager = $this->createMock(ObjectManager::class);
        $reflection = new \ReflectionClass($entity);

        $metadata = $this->createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflection);

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($metadata);

        // 直接测试 prePersistEntity 方法
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

        $objectManager = $this->createMock(ObjectManager::class);
        $reflection = new \ReflectionClass($entity);

        $metadata = $this->createMock(\Doctrine\ORM\Mapping\ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflection);

        $objectManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($metadata);

        // 没有 UUID 属性的情况下，logger 不应被调用
        $this->logger->expects($this->never())
            ->method('debug');

        $this->listener->prePersistEntity($objectManager, $entity);
    }
}
