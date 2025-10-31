<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Attribute\When;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;

#[When(env: 'test')]
#[When(env: 'dev')]
class SettlementIncomeDataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // 创建一个测试用的公众号账号
        $account = new Account();
        $account->setName('测试公众号-结算收入');
        $account->setAppId('test_official_account_si_001');
        $account->setAppSecret('test_secret_si_001');
        $account->setValid(true);
        $manager->persist($account);

        
        $settlementIncomeData = null;
        for ($i = 1; $i <= 6; ++$i) {
            // 上半月数据
            $settlementIncomeData = new SettlementIncomeData();
            $settlementIncomeData->setAccount($account);
            $settlementIncomeData->setDate(new \DateTimeImmutable(sprintf('2024-%02d-01', $i)));
            $settlementIncomeData->setBody('测试主体' . $i);
            $settlementIncomeData->setPenaltyAll(100 + $i * 10);
            $settlementIncomeData->setRevenueAll(50000 + $i * 5000);
            $settlementIncomeData->setSettledRevenueAll(40000 + $i * 4000);
            $settlementIncomeData->setZone(sprintf('2024-%02d-01至2024-%02d-15', $i, $i));
            $settlementIncomeData->setMonth(sprintf('2024-%02d', $i));
            $settlementIncomeData->setOrder(SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH);
            $settlementIncomeData->setSettStatus(SettlementIncomeOrderStatusEnum::SETTLED);
            $settlementIncomeData->setSettledRevenue(20000 + $i * 2000);
            $settlementIncomeData->setSettNo('SETT' . $i . 'A');
            $settlementIncomeData->setMailSendCnt('0');
            $settlementIncomeData->setSlotId('slot_' . $i);
            $settlementIncomeData->setSlotSettledRevenue(10000 + $i * 1000);
            $manager->persist($settlementIncomeData);

            // 下半月数据
            $settlementIncomeData = new SettlementIncomeData();
            $settlementIncomeData->setAccount($account);
            $settlementIncomeData->setDate(new \DateTimeImmutable(sprintf('2024-%02d-16', $i)));
            $settlementIncomeData->setBody('测试主体' . $i);
            $settlementIncomeData->setPenaltyAll(150 + $i * 15);
            $settlementIncomeData->setRevenueAll(60000 + $i * 6000);
            $settlementIncomeData->setSettledRevenueAll(50000 + $i * 5000);
            $settlementIncomeData->setZone(sprintf('2024-%02d-16至2024-%02d-28', $i, $i));
            $settlementIncomeData->setMonth(sprintf('2024-%02d', $i));
            $settlementIncomeData->setOrder(SettlementIncomeOrderTypeEnum::SECOND_HALF_OF_MONTH);
            $settlementIncomeData->setSettStatus(SettlementIncomeOrderStatusEnum::PAID);
            $settlementIncomeData->setSettledRevenue(30000 + $i * 3000);
            $settlementIncomeData->setSettNo('SETT' . $i . 'B');
            $settlementIncomeData->setMailSendCnt('0');
            $settlementIncomeData->setSlotId('slot_' . $i);
            $settlementIncomeData->setSlotSettledRevenue(15000 + $i * 1500);
            $manager->persist($settlementIncomeData);
        }

        $manager->flush();

        // 添加引用，供其他 Fixtures 使用
        $this->addReference(self::class . '_account', $account);
        // 循环至少执行一次，所以 $settlementIncomeData 一定有值
        /** @var SettlementIncomeData $settlementIncomeData */
        $this->addReference(self::class . '_settlement_income_data_1', $settlementIncomeData);
    }
}
