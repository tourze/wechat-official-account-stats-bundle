<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractBundleTestCase;
use WechatOfficialAccountStatsBundle\WechatOfficialAccountStatsBundle;

/**
 * @internal
 */
#[CoversClass(WechatOfficialAccountStatsBundle::class)]
#[RunTestsInSeparateProcesses]
final class WechatOfficialAccountStatsBundleTest extends AbstractBundleTestCase
{
}
