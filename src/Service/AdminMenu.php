<?php

namespace WechatOfficialAccountStatsBundle\Service;

use Knp\Menu\ItemInterface;
use Tourze\EasyAdminMenuBundle\Service\LinkGeneratorInterface;
use Tourze\EasyAdminMenuBundle\Service\MenuProviderInterface;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareData;
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummaryHour;
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;
use WechatOfficialAccountStatsBundle\Entity\MessageSendWeekData;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;

/**
 * 微信公众号统计菜单服务
 */
readonly class AdminMenu implements MenuProviderInterface
{
    public function __construct(
        private LinkGeneratorInterface $linkGenerator,
    ) {
    }

    public function __invoke(ItemInterface $item): void
    {
        $this->addWechatStatsMenu($item);
    }

    private function addWechatStatsMenu(ItemInterface $item): void
    {
        if (null === $item->getChild('微信统计')) {
            $item->addChild('微信统计');
        }

        $wechatMenu = $item->getChild('微信统计');
        if (null === $wechatMenu) {
            return;
        }

        $this->addUserDataMenu($wechatMenu);
        $this->addMessageDataMenu($wechatMenu);
        $this->addArticleDataMenu($wechatMenu);
        $this->addInterfaceDataMenu($wechatMenu);
        $this->addAdDataMenu($wechatMenu);
    }

    private function addUserDataMenu(ItemInterface $wechatMenu): void
    {
        if (null === $wechatMenu->getChild('用户数据')) {
            $wechatMenu->addChild('用户数据');
        }
        $userMenu = $wechatMenu->getChild('用户数据');
        if (null !== $userMenu) {
            $userMenu->addChild('用户概况')
                ->setUri($this->linkGenerator->getCurdListPage(UserSummary::class))
                ->setAttribute('icon', 'fas fa-users')
            ;

            $userMenu->addChild('用户累计')
                ->setUri($this->linkGenerator->getCurdListPage(UserCumulate::class))
                ->setAttribute('icon', 'fas fa-user-plus')
            ;
        }
    }

    private function addMessageDataMenu(ItemInterface $wechatMenu): void
    {
        if (null === $wechatMenu->getChild('消息数据')) {
            $wechatMenu->addChild('消息数据');
        }
        $messageMenu = $wechatMenu->getChild('消息数据');
        if (null !== $messageMenu) {
            $messageMenu->addChild('消息发送日报')
                ->setUri($this->linkGenerator->getCurdListPage(MessageSendData::class))
                ->setAttribute('icon', 'fas fa-paper-plane')
            ;

            $messageMenu->addChild('消息发送时报')
                ->setUri($this->linkGenerator->getCurdListPage(MessageSendHourData::class))
                ->setAttribute('icon', 'fas fa-clock')
            ;

            $messageMenu->addChild('消息发送周报')
                ->setUri($this->linkGenerator->getCurdListPage(MessageSendWeekData::class))
                ->setAttribute('icon', 'fas fa-calendar-week')
            ;

            $messageMenu->addChild('消息发送月报')
                ->setUri($this->linkGenerator->getCurdListPage(MessageSendMonthData::class))
                ->setAttribute('icon', 'fas fa-calendar-alt')
            ;

            $messageMenu->addChild('消息发送分布')
                ->setUri($this->linkGenerator->getCurdListPage(MessageSenDistData::class))
                ->setAttribute('icon', 'fas fa-chart-pie')
            ;
        }
    }

    private function addArticleDataMenu(ItemInterface $wechatMenu): void
    {
        if (null === $wechatMenu->getChild('图文数据')) {
            $wechatMenu->addChild('图文数据');
        }
        $articleMenu = $wechatMenu->getChild('图文数据');
        if (null !== $articleMenu) {
            $articleMenu->addChild('图文统计日报')
                ->setUri($this->linkGenerator->getCurdListPage(ImageTextStatistics::class))
                ->setAttribute('icon', 'fas fa-images')
            ;

            $articleMenu->addChild('图文统计时报')
                ->setUri($this->linkGenerator->getCurdListPage(ImageTextStatisticsHour::class))
                ->setAttribute('icon', 'fas fa-image')
            ;

            $articleMenu->addChild('图文分享日报')
                ->setUri($this->linkGenerator->getCurdListPage(ImageTextShareData::class))
                ->setAttribute('icon', 'fas fa-share-alt')
            ;

            $articleMenu->addChild('图文分享时报')
                ->setUri($this->linkGenerator->getCurdListPage(ImageTextShareDataHour::class))
                ->setAttribute('icon', 'fas fa-share')
            ;

            $articleMenu->addChild('文章概况')
                ->setUri($this->linkGenerator->getCurdListPage(ArticleDailySummary::class))
                ->setAttribute('icon', 'fas fa-file-alt')
            ;

            $articleMenu->addChild('文章总数')
                ->setUri($this->linkGenerator->getCurdListPage(ArticleTotal::class))
                ->setAttribute('icon', 'fas fa-list-ol')
            ;
        }
    }

    private function addInterfaceDataMenu(ItemInterface $wechatMenu): void
    {
        if (null === $wechatMenu->getChild('接口数据')) {
            $wechatMenu->addChild('接口数据');
        }
        $interfaceMenu = $wechatMenu->getChild('接口数据');
        if (null !== $interfaceMenu) {
            $interfaceMenu->addChild('接口分析日报')
                ->setUri($this->linkGenerator->getCurdListPage(InterfaceSummary::class))
                ->setAttribute('icon', 'fas fa-plug')
            ;

            $interfaceMenu->addChild('接口分析时报')
                ->setUri($this->linkGenerator->getCurdListPage(InterfaceSummaryHour::class))
                ->setAttribute('icon', 'fas fa-stopwatch')
            ;
        }
    }

    private function addAdDataMenu(ItemInterface $wechatMenu): void
    {
        if (null === $wechatMenu->getChild('广告与收益')) {
            $wechatMenu->addChild('广告与收益');
        }
        $adMenu = $wechatMenu->getChild('广告与收益');
        if (null !== $adMenu) {
            $adMenu->addChild('广告位数据')
                ->setUri($this->linkGenerator->getCurdListPage(AdvertisingSpaceData::class))
                ->setAttribute('icon', 'fas fa-ad')
            ;

            $adMenu->addChild('返佣商品数据')
                ->setUri($this->linkGenerator->getCurdListPage(RebateGoodsData::class))
                ->setAttribute('icon', 'fas fa-gift')
            ;

            $adMenu->addChild('结算收入数据')
                ->setUri($this->linkGenerator->getCurdListPage(SettlementIncomeData::class))
                ->setAttribute('icon', 'fas fa-money-bill-wave')
            ;
        }
    }
}
