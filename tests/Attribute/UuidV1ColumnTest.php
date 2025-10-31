<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\Tests\Attribute;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;

/**
 * @internal
 */
#[CoversClass(UuidV1Column::class)]
final class UuidV1ColumnTest extends TestCase
{
    public function testAttributeCanBeInstantiated(): void
    {
        $attribute = new UuidV1Column();
        $this->assertInstanceOf(UuidV1Column::class, $attribute);
    }

    public function testAttributeTargetIsProperty(): void
    {
        $reflection = new \ReflectionClass(UuidV1Column::class);
        $attributes = $reflection->getAttributes(\Attribute::class);

        $this->assertNotEmpty($attributes);
        $attributeInstance = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags);
    }
}
