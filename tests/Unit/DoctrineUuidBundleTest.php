<?php

namespace Tourze\DoctrineUuidBundle\Tests\Unit;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use Tourze\DoctrineEntityCheckerBundle\DoctrineEntityCheckerBundle;
use Tourze\DoctrineUuidBundle\DoctrineUuidBundle;

class DoctrineUuidBundleTest extends TestCase
{
    private DoctrineUuidBundle $bundle;

    protected function setUp(): void
    {
        $this->bundle = new DoctrineUuidBundle();
    }

    public function testBundleExtendsCorrectClass(): void
    {
        $this->assertInstanceOf(Bundle::class, $this->bundle);
    }

    public function testBundleImplementsCorrectInterface(): void
    {
        $this->assertInstanceOf(BundleDependencyInterface::class, $this->bundle);
    }

    public function testGetBundleDependenciesReturnCorrectDependencies(): void
    {
        $dependencies = DoctrineUuidBundle::getBundleDependencies();

        $this->assertArrayHasKey(DoctrineBundle::class, $dependencies);
        $this->assertArrayHasKey(DoctrineEntityCheckerBundle::class, $dependencies);
        
        $this->assertEquals(['all' => true], $dependencies[DoctrineBundle::class]);
        $this->assertEquals(['all' => true], $dependencies[DoctrineEntityCheckerBundle::class]);
    }

    public function testGetBundleDependenciesReturnsTwoElements(): void
    {
        $dependencies = DoctrineUuidBundle::getBundleDependencies();
        
        $this->assertCount(2, $dependencies);
    }
}