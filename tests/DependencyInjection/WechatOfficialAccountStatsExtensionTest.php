<?php

namespace WechatOfficialAccountStatsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use WechatOfficialAccountStatsBundle\DependencyInjection\WechatOfficialAccountStatsExtension;

class WechatOfficialAccountStatsExtensionTest extends TestCase
{
    private WechatOfficialAccountStatsExtension $extension;
    private ContainerBuilder $container;

    protected function setUp(): void
    {
        $this->extension = new WechatOfficialAccountStatsExtension();
        $this->container = new ContainerBuilder();
    }

    /**
     * 测试扩展能够正确加载
     */
    public function testLoad_withEmptyConfig_loadsSuccessfully(): void
    {
        $configs = [];
        
        // 测试扩展能够加载而不抛出异常
        $this->extension->load($configs, $this->container);
        
        $this->assertTrue(true); // 如果到达这里，说明加载成功
    }

    /**
     * 测试扩展别名
     */
    public function testGetAlias_returnsCorrectAlias(): void
    {
        $expectedAlias = 'wechat_official_account_stats';
        $this->assertSame($expectedAlias, $this->extension->getAlias());
    }
}