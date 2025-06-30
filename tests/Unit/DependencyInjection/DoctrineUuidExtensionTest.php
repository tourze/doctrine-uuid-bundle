<?php

namespace Tourze\DoctrineUuidBundle\Tests\Unit\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\DoctrineUuidBundle\DependencyInjection\DoctrineUuidExtension;
use Tourze\DoctrineUuidBundle\EventSubscriber\UuidListener;

class DoctrineUuidExtensionTest extends TestCase
{
    private DoctrineUuidExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new DoctrineUuidExtension();
        $this->container = new ContainerBuilder();
    }

    public function testLoadServicesConfiguration(): void
    {
        $this->extension->load([], $this->container);

        // 验证 property accessor 服务已加载
        $this->assertTrue($this->container->hasDefinition('doctrine-uuid.property-accessor'));
        
        $definition = $this->container->getDefinition('doctrine-uuid.property-accessor');
        $this->assertNotNull($definition);
        $this->assertEquals('Symfony\Component\PropertyAccess\PropertyAccessor', $definition->getClass());
        
        // 验证工厂方法设置
        $factory = $definition->getFactory();
        $this->assertIsArray($factory);
        $this->assertEquals('Symfony\Component\PropertyAccess\PropertyAccess', $factory[0]);
        $this->assertEquals('createPropertyAccessor', $factory[1]);
    }

    public function testLoadWithEmptyConfig(): void
    {
        $this->extension->load([], $this->container);
        
        // 验证容器状态
        $this->assertGreaterThan(0, count($this->container->getDefinitions()));
    }
}