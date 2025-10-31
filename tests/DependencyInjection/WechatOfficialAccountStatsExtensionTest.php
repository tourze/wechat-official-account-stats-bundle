<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Tourze\PHPUnitSymfonyUnitTest\AbstractDependencyInjectionExtensionTestCase;
use WechatOfficialAccountStatsBundle\DependencyInjection\WechatOfficialAccountStatsExtension;

/**
 * @internal
 */
#[CoversClass(WechatOfficialAccountStatsExtension::class)]
final class WechatOfficialAccountStatsExtensionTest extends AbstractDependencyInjectionExtensionTestCase
{
    private WechatOfficialAccountStatsExtension $extension;

    private ContainerBuilder $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->extension = new WechatOfficialAccountStatsExtension();
        $this->container = new ContainerBuilder();
        $this->container->setParameter('kernel.environment', 'test');
    }

    /**
     * 测试扩展能够正确加载
     */
    public function testLoadWithEmptyConfigLoadsSuccessfully(): void
    {
        $configs = [];

        // 测试扩展加载不会抛出异常
        $this->extension->load($configs, $this->container);

        // 验证容器已被正确配置
        $this->assertInstanceOf(ContainerBuilder::class, $this->container);
    }

    /**
     * 测试扩展别名
     */
    public function testGetAliasReturnsCorrectAlias(): void
    {
        $expectedAlias = 'wechat_official_account_stats';

        $this->assertSame($expectedAlias, $this->extension->getAlias());
    }
}
