<?php

namespace Tourze\DoctrineUuidBundle\Tests\Unit\Attribute;

use PHPUnit\Framework\TestCase;
use Tourze\DoctrineUuidBundle\Attribute\UuidV4Column;

class UuidV4ColumnTest extends TestCase
{
    public function testAttributeCanBeInstantiated(): void
    {
        $attribute = new UuidV4Column();
        $this->assertInstanceOf(UuidV4Column::class, $attribute);
    }

    public function testAttributeTargetIsProperty(): void
    {
        $reflection = new \ReflectionClass(UuidV4Column::class);
        $attributes = $reflection->getAttributes(\Attribute::class);
        
        $this->assertNotEmpty($attributes);
        $attributeInstance = $attributes[0]->newInstance();
        $this->assertEquals(\Attribute::TARGET_PROPERTY, $attributeInstance->flags);
    }
}