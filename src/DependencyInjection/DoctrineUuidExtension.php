<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\DependencyInjection;

use Tourze\SymfonyDependencyServiceLoader\AutoExtension;

/**
 * @internal
 */
class DoctrineUuidExtension extends AutoExtension
{
    protected function getConfigDir(): string
    {
        return __DIR__ . '/../Resources/config';
    }
}
