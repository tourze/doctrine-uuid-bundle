<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration\Entity;

use PHPUnit\Framework\TestCase;

class TestEntityTest extends TestCase
{
    public function testEntityCanBeInstantiated(): void
    {
        $entity = new TestEntity();
        $this->assertInstanceOf(TestEntity::class, $entity);
    }

    public function testGetterAndSetterForId(): void
    {
        $entity = new TestEntity();
        $id = 'test-uuid-id';
        
        $entity->setId($id);
        $this->assertEquals($id, $entity->getId());
    }

    public function testGetterAndSetterForUuid(): void
    {
        $entity = new TestEntity();
        $uuid = 'test-uuid-value';
        
        $entity->setUuid($uuid);
        $this->assertEquals($uuid, $entity->getUuid());
        
        // 测试 null 值
        $entity->setUuid(null);
        $this->assertNull($entity->getUuid());
    }

    public function testGetterAndSetterForName(): void
    {
        $entity = new TestEntity();
        $name = 'Test Name';
        
        $entity->setName($name);
        $this->assertEquals($name, $entity->getName());
    }

    public function testToStringMethod(): void
    {
        $entity = new TestEntity();
        
        // 测试默认情况下返回 ID
        $id = 'test-id';
        $entity->setId($id);
        $this->assertEquals($id, (string) $entity);
        
        // 测试设置名称后返回名称
        $name = 'Test Name';
        $entity->setName($name);
        $this->assertEquals($name, (string) $entity);
    }

    public function testFluentInterface(): void
    {
        $entity = new TestEntity();
        
        // 测试链式调用
        $result = $entity->setId('id')
            ->setUuid('uuid')
            ->setName('name');
            
        $this->assertSame($entity, $result);
    }
}