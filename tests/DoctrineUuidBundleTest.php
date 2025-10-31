<?php

declare(strict_types=1);

namespace Tourze\DoctrineUuidBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\DoctrineUuidBundle\DoctrineUuidBundle;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;

/**
 * @internal
 */
#[CoversClass(DoctrineUuidBundle::class)]
#[RunTestsInSeparateProcesses]
final class DoctrineUuidBundleTest extends AbstractBundleTestCase
{
}
