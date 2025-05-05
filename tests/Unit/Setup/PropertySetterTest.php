<?php

namespace Tourze\DoctrineUuidBundle\Tests\Unit\Setup;

use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

class PropertySetterTest extends TestCase
{
    public function testPropertySetterWithUuidV1(): void
    {
        $entity = new class {
            #[UuidV1Column]
            private string $id = '';

            public function getId(): string
            {
                return $this->id;
            }

            public function setId(string $id): self
            {
                $this->id = $id;
                return $this;
            }
        };

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('id');

        $this->assertTrue($propertyAccessor->isWritable($entity, 'id'));
        $this->assertNotEmpty($property->getAttributes(UuidV1Column::class));

        $propertyAccessor->setValue($entity, 'id', 'test-uuid-value');
        $this->assertEquals('test-uuid-value', $entity->getId());
    }

    public function testPropertySetterWithUuidV4(): void
    {
        $entity = new class {
            #[UuidV4Column]
            private ?string $uuid = null;

            public function getUuid(): ?string
            {
                return $this->uuid;
            }

            public function setUuid(?string $uuid): self
            {
                $this->uuid = $uuid;
                return $this;
            }
        };

        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $reflection = new \ReflectionClass($entity);
        $property = $reflection->getProperty('uuid');

        $this->assertTrue($propertyAccessor->isWritable($entity, 'uuid'));
        $this->assertNotEmpty($property->getAttributes(UuidV4Column::class));

        $propertyAccessor->setValue($entity, 'uuid', 'test-uuid-value');
        $this->assertEquals('test-uuid-value', $entity->getUuid());
    }
}
