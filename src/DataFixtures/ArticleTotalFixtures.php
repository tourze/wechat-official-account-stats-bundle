<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ArticleTotal;

#[When(env: 'test')]
#[When(env: 'dev')]
class ArticleTotalFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_artt_001');
        $account->setAppSecret('test_secret_artt_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $titles = [
            '深入理解 Symfony 事件系统',
            'PHP 性能优化实战',
            '微服务架构设计模式',
            '前端框架对比分析',
            'DevOps 最佳实践',
        ];

        
        $articleTotal = null;
        for ($i = 1; $i <= 8; ++$i) {
            $articleTotal = new ArticleTotal();
            $articleTotal->setAccount($account);
            $articleTotal->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $articleTotal->setStatDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i + 1)));
            $articleTotal->setMsgId('total_msg_' . $i . '_001');
            $articleTotal->setTitle($titles[($i - 1) % count($titles)]);
            $articleTotal->setTargetUser(5000 + $i * 500);
            $articleTotal->setIntPageReadUser(2000 + $i * 200);
            $articleTotal->setIntPageReadCount(3000 + $i * 300);
            $articleTotal->setOriPageReadUser(400 + $i * 40);
            $articleTotal->setOriPageReadCount(500 + $i * 50);
            $articleTotal->setShareUser(100 + $i * 10);
            $articleTotal->setShareCount(140 + $i * 14);
            $articleTotal->setAddToFavUser(20 + $i * 2);
            $articleTotal->setAddToFavCount(30 + $i * 3);
            $articleTotal->setIntPageFromSessionReadUser(300 + $i * 30);
            $articleTotal->setIntPageFromSessionReadCount(450 + $i * 45);
            $articleTotal->setIntPageFromHistMsgReadUser(200 + $i * 20);
            $articleTotal->setIntPageFromHistMsgReadCount(300 + $i * 30);
            $articleTotal->setIntPageFromFeedReadUser(150 + $i * 15);
            $articleTotal->setIntPageFromFeedReadCount(225 + $i * 22);
            $articleTotal->setIntPageFromFriendsReadUser(100 + $i * 10);
            $articleTotal->setIntPageFromFriendsReadCount(150 + $i * 15);
            $articleTotal->setIntPageFromOtherReadUser(50 + $i * 5);
            $articleTotal->setIntPageFromOtherReadCount(75 + $i * 7);
            $articleTotal->setFeedShareFromSessionUser(20 + $i * 2);
            $articleTotal->setFeedShareFromSessionCnt(30 + $i * 3);
            $articleTotal->setFeedShareFromFeedUser(15 + $i);
            $articleTotal->setFeedShareFromFeedCnt(22 + $i * 2);
            $articleTotal->setFeedShareFromOtherUser(10 + $i);
            $articleTotal->setFeedShareFromOtherCnt(15 + $i);
            $manager->persist($articleTotal);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $articleTotal 一定有值
        /** @var ArticleTotal $articleTotal */
        $this->addReference(self::class . '_article_total_1', $articleTotal);
    }
}
