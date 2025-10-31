<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

/**
 * @internal
 */
#[CoversClass(UuidV1Column::class)]
final class UuidColumnAttributeTest extends TestCase
{
    public function testUuidV1ColumnAttribute(): void
    {
        $attribute = new UuidV1Column();
        $this->assertInstanceOf(UuidV1Column::class, $attribute);
    }

    public function testUuidV4ColumnAttribute(): void
    {
        $attribute = new UuidV4Column();
        $this->assertInstanceOf(UuidV4Column::class, $attribute);
    }

    public function testUuidV1ColumnAttributeReflection(): void
    {
        $reflection = new \ReflectionClass(UuidV1Column::class);
        $attributes = $reflection->getAttributes();

        $this->assertCount(1, $attributes);
        $this->assertEquals(\Attribute::class, $attributes[0]->getName());

        $attributeInstance = $attributes[0]->newInstance();
        $this->assertInstanceOf(\Attribute::class, $attributeInstance);
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags);
    }

    public function testUuidV4ColumnAttributeReflection(): void
    {
        $reflection = new \ReflectionClass(UuidV4Column::class);
        $attributes = $reflection->getAttributes();

        $this->assertCount(1, $attributes);
        $this->assertEquals(\Attribute::class, $attributes[0]->getName());

        $attributeInstance = $attributes[0]->newInstance();
        $this->assertInstanceOf(\Attribute::class, $attributeInstance);
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags);
    }
}
