<?php

namespace WechatOfficialAccountStatsBundle\Tests\Service;

use Knp\Menu\ItemInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use Tourze\PHPUnitSymfonyWebTest\AbstractEasyAdminMenuTestCase;
use WechatOfficialAccountStatsBundle\Service\AdminMenu;

/**
 * @internal
 */
#[CoversClass(AdminMenu::class)]
#[RunTestsInSeparateProcesses]
final class AdminMenuTest extends AbstractEasyAdminMenuTestCase
{
    public function testCanBeInstantiated(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(AdminMenu::class, $adminMenu);
    }

    public function testInvokeCreatesWechatStatsMenu(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $menuItem = $this->createMock(ItemInterface::class);
        $wechatMenu = $this->createMock(ItemInterface::class);

        // First call in addWechatStatsMenu() line 44
        $menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('微信统计')
            ->willReturnOnConsecutiveCalls(null, $wechatMenu)
        ;

        $menuItem->expects($this->once())
            ->method('addChild')
            ->with('微信统计')
            ->willReturn($wechatMenu)
        ;

        // Mock the sub-menu creation calls for the created wechat menu
        $wechatMenu->method('getChild')->willReturn(null);
        $wechatMenu->method('addChild')->willReturn($this->createMock(ItemInterface::class));

        $adminMenu($menuItem);
    }

    public function testInvokeWithExistingWechatStatsMenu(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $menuItem = $this->createMock(ItemInterface::class);
        $wechatMenu = $this->createMock(ItemInterface::class);

        $menuItem->expects($this->exactly(2))
            ->method('getChild')
            ->with('微信统计')
            ->willReturn($wechatMenu)
        ;

        $menuItem->expects($this->never())
            ->method('addChild')
        ;

        // Mock the sub-menu creation calls
        $wechatMenu->method('getChild')->willReturn(null);
        $wechatMenu->method('addChild')->willReturn($this->createMock(ItemInterface::class));

        $adminMenu($menuItem);
    }

    public function testImplementsCorrectInterface(): void
    {
        $adminMenu = self::getService(AdminMenu::class);
        $this->assertInstanceOf(MenuProviderInterface::class, $adminMenu);
    }

    protected function onSetUp(): void
    {
        // Required by parent class
    }
}
