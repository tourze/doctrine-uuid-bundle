<?php

namespace Tourze\DoctrineUuidBundle\Tests\Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUuidBundle\Attribute\UuidV1Column;

class UuidV1ColumnTest extends TestCase
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