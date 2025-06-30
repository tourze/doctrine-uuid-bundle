<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration\EventSubscriber;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Mapping\ClassMetadata;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;

class UuidListenerTest extends TestCase
{
    private UuidListener $listener;
    private EntityManagerInterface $entityManager;
    private LoggerInterface $logger;

    protected function setUp(): void
    {
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->listener = new UuidListener(
            PropertyAccess::createPropertyAccessor(),
            $this->logger
        );
    }

    private function setupEntityManagerForEntity(object $entity): void
    {
        $reflection = new \ReflectionClass($entity);
        $metadata = $this->createMock(ClassMetadata::class);
        $metadata->expects($this->once())
            ->method('getReflectionClass')
            ->willReturn($reflection);

        $this->entityManager->expects($this->once())
            ->method('getClassMetadata')
            ->with($entity::class)
            ->willReturn($metadata);
    }

    public function testPrePersistWithUuidV1Column(): void
    {
        $entity = new class {
            #[UuidV1Column]
            private ?string $uuid = null;

            public function getUuid(): ?string
            {
                return $this->uuid;
            }

            public function setUuid(?string $uuid): void
            {
                $this->uuid = $uuid;
            }
        };

        $this->logger->expects($this->once())
            ->method('debug')
            ->with(
                $this->stringContains('为uuid分配UUID v1 ID'),
                $this->callback(function ($context) use ($entity) {
                    return isset($context['id']) && isset($context['entity']) && $context['entity'] === $entity;
                })
            );

        $this->setupEntityManagerForEntity($entity);
        
        $args = new PrePersistEventArgs($entity, $this->entityManager);
        $this->listener->prePersist($args);

        $this->assertNotNull($entity->getUuid());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $entity->getUuid());
    }

    public function testPrePersistWithUuidV4Column(): void
    {
        $entity = new class {
            #[UuidV4Column]
            private ?string $uuid = null;

            public function getUuid(): ?string
            {
                return $this->uuid;
            }

            public function setUuid(?string $uuid): void
            {
                $this->uuid = $uuid;
            }
        };

        $this->logger->expects($this->once())
            ->method('debug')
            ->with(
                $this->stringContains('为uuid分配UUID v4 ID'),
                $this->callback(function ($context) use ($entity) {
                    return isset($context['id']) && isset($context['entity']) && $context['entity'] === $entity;
                })
            );

        $this->setupEntityManagerForEntity($entity);
        
        $args = new PrePersistEventArgs($entity, $this->entityManager);
        $this->listener->prePersist($args);

        $this->assertNotNull($entity->getUuid());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $entity->getUuid());
    }

    public function testPrePersistWithExistingUuidValue(): void
    {
        $existingUuid = '123e4567-e89b-12d3-a456-426614174000';
        
        $entity = new class($existingUuid) {
            #[UuidV4Column]
            private ?string $uuid;

            public function __construct(?string $uuid)
            {
                $this->uuid = $uuid;
            }

            public function getUuid(): ?string
            {
                return $this->uuid;
            }
        };

        $this->logger->expects($this->never())
            ->method('debug');

        $this->setupEntityManagerForEntity($entity);
        
        $args = new PrePersistEventArgs($entity, $this->entityManager);
        $this->listener->prePersist($args);

        $this->assertEquals($existingUuid, $entity->getUuid());
    }

    public function testPrePersistWithMultipleUuidColumns(): void
    {
        $entity = new class {
            #[UuidV1Column]
            private ?string $uuidV1 = null;

            #[UuidV4Column]
            private ?string $uuidV4 = null;

            public function getUuidV1(): ?string
            {
                return $this->uuidV1;
            }

            public function setUuidV1(?string $uuid): void
            {
                $this->uuidV1 = $uuid;
            }

            public function getUuidV4(): ?string
            {
                return $this->uuidV4;
            }

            public function setUuidV4(?string $uuid): void
            {
                $this->uuidV4 = $uuid;
            }
        };

        $this->logger->expects($this->exactly(2))
            ->method('debug');

        $this->setupEntityManagerForEntity($entity);
        
        $args = new PrePersistEventArgs($entity, $this->entityManager);
        $this->listener->prePersist($args);

        $this->assertNotNull($entity->getUuidV1());
        $this->assertNotNull($entity->getUuidV4());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-1[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $entity->getUuidV1());
        $this->assertMatchesRegularExpression('/^[0-9a-f]{8}-[0-9a-f]{4}-4[0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}$/i', $entity->getUuidV4());
    }

    public function testPrePersistWithNonWritableProperty(): void
    {
        $entity = new class {
            #[UuidV4Column]
            private string $readonlyUuid = '';

            public function getReadonlyUuid(): string
            {
                return $this->readonlyUuid;
            }
        };

        $this->logger->expects($this->never())
            ->method('debug');

        $this->setupEntityManagerForEntity($entity);
        
        $args = new PrePersistEventArgs($entity, $this->entityManager);
        $this->listener->prePersist($args);

        $this->assertEquals('', $entity->getReadonlyUuid());
    }
}