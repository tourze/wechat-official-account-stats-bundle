<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;

#[When(env: 'test')]
#[When(env: 'dev')]
class ArticleDailySummaryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号');
        $account->setAppId('test_official_account_artd_001');
        $account->setAppSecret('test_secret_artd_001');
        $account->setValid(true);
        $manager->persist($account);

        // 创建测试数据
        $titles = [
            '如何提高工作效率',
            'PHP 最佳实践指南',
            'Symfony 框架入门教程',
            '数据库优化技巧',
            '前端开发趋势分析',
        ];

        
        $articleDailySummary = null;
        for ($i = 1; $i <= 10; ++$i) {
            $articleDailySummary = new ArticleDailySummary();
            $articleDailySummary->setAccount($account);
            $articleDailySummary->setDate(new \DateTimeImmutable(sprintf('2024-01-%02d', $i)));
            $articleDailySummary->setMsgId('msg_' . $i . '_001');
            $articleDailySummary->setTitle($titles[($i - 1) % count($titles)]);
            $articleDailySummary->setIntPageReadUser(1000 + $i * 100);
            $articleDailySummary->setIntPageReadCount(1500 + $i * 150);
            $articleDailySummary->setOriPageReadUser(200 + $i * 20);
            $articleDailySummary->setOriPageReadCount(250 + $i * 25);
            $articleDailySummary->setShareUser(50 + $i * 5);
            $articleDailySummary->setShareCount(70 + $i * 7);
            $articleDailySummary->setAddToFavUser(10 + $i);
            $articleDailySummary->setAddToFavCount(15 + $i);
            $manager->persist($articleDailySummary);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $articleDailySummary 一定有值
        /** @var ArticleDailySummary $articleDailySummary */
        $this->addReference(self::class . '_article_1', $articleDailySummary);
    }
}
