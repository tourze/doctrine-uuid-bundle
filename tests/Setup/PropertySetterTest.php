<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\Tests\Setup;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

/**
 * @internal
 */
#[CoversClass(UuidV1Column::class)]
final class PropertySetterTest extends TestCase
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

            public function setId(string $id): void
            {
                $this->id = $id;
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

            public function setUuid(?string $uuid): void
            {
                $this->uuid = $uuid;
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
