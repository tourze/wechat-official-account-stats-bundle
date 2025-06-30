<?php

namespace WechatOfficialAccountStatsBundle\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use WechatOfficialAccountStatsBundle\WechatOfficialAccountStatsBundle;

class WechatOfficialAccountStatsBundleTest extends TestCase
{
    public function testBundleExtendsBundle(): void
    {
        $bundle = new WechatOfficialAccountStatsBundle();
        $this->assertInstanceOf(Bundle::class, $bundle);
    }

    public function testBundleName(): void
    {
        $bundle = new WechatOfficialAccountStatsBundle();
        $this->assertSame('WechatOfficialAccountStatsBundle', $bundle->getName());
    }
}