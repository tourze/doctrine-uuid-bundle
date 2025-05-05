<?php

namespace Tourze\DoctrineUuidBundle\Tests\Integration;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel;
use Tourze\DoctrineEntityCheckerBundle\DoctrineEntityCheckerBundle;
use Tourze\DoctrineUuidBundle\DoctrineUuidBundle;

class IntegrationTestKernel extends Kernel
{
    use MicroKernelTrait;

    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new DoctrineBundle(),
            new DoctrineEntityCheckerBundle(),
            new DoctrineUuidBundle(),
        ];
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->loadFromExtension('framework', [
            'test' => true,
            'secret' => 'test',
        ]);

        $container->loadFromExtension('doctrine', [
            'dbal' => [
                'driver' => 'pdo_sqlite',
                'path' => '%kernel.cache_dir%/test.db',
                'charset' => 'UTF8',
            ],
            'orm' => [
                'auto_generate_proxy_classes' => true,
                'auto_mapping' => true,
                'mappings' => [
                    'TestEntities' => [
                        'is_bundle' => false,
                        'type' => 'attribute',
                        'dir' => '%kernel.project_dir%/tests/Integration/Entity',
                        'prefix' => 'Tourze\DoctrineUuidBundle\Tests\Integration\Entity',
                    ],
                ],
            ],
        ]);
    }
}
