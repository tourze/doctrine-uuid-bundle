<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\DoctrineUuidBundle\DependencyInjection\DoctrineUuidExtension;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineUuidExtension::class)]
final class DoctrineUuidExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
}
