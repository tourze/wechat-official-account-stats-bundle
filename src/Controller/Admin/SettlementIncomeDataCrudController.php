<?php

namespace WechatOfficialAccountStatsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatOfficialAccountStatsBundle\Entity\SettlementIncomeData;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderStatusEnum;
use WechatOfficialAccountStatsBundle\Enum\SettlementIncomeOrderTypeEnum;

/**
 * 结算收入数据CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/settlement-income-data', routeName: 'wechat_stats_settlement_income_data')]
final class SettlementIncomeDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SettlementIncomeData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('结算收入数据')
            ->setEntityLabelInPlural('结算收入数据列表')
            ->setPageTitle('index', '结算收入数据统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date', 'body', 'settNo'])
            ->setHelp('index', '微信公众号结算收入数据统计')
        ;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::NEW, Action::EDIT, Action::DELETE)
        ;
    }

    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add('date')
            ->add('order')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield TextField::new('body', '主体名称')
            ->setHelp('结算主体名称')
        ;

        yield TextField::new('month', '收入月份')
            ->setHelp('收入的月份')
        ;

        yield TextField::new('zone', '日期区间')
            ->setHelp('收入的日期区间')
        ;

        yield ChoiceField::new('order', '结算周期')
            ->setChoices([
                '上半月' => SettlementIncomeOrderTypeEnum::FIRST_HALF_OF_MONTH,
                '下半月' => SettlementIncomeOrderTypeEnum::SECOND_HALF_OF_MONTH,
            ])
            ->setHelp('1=上半月，2=下半月')
        ;

        yield ChoiceField::new('settStatus', '结算状态')
            ->setChoices([
                '结算中' => SettlementIncomeOrderStatusEnum::SETTLING,
                '已结算' => SettlementIncomeOrderStatusEnum::SETTLED,
                '已结算2' => SettlementIncomeOrderStatusEnum::SETTLED_TWO,
                '付款中' => SettlementIncomeOrderStatusEnum::PAYMENT_PENDING,
                '已付款' => SettlementIncomeOrderStatusEnum::PAID,
            ])
            ->setHelp('1=结算中；2、3=已结算；4=付款中；5=已付款')
        ;

        yield IntegerField::new('revenueAll', '累计收入')
            ->setHelp('累计收入，单位：分')
        ;

        yield IntegerField::new('penaltyAll', '扣除金额')
            ->setHelp('扣除金额，单位：分')
        ;

        yield IntegerField::new('settledRevenueAll', '已结算金额')
            ->setHelp('已结算金额，单位：分')
        ;

        yield IntegerField::new('settledRevenue', '区间内结算收入')
            ->setHelp('区间内结算收入，单位：分')
        ;

        yield TextField::new('settNo', '结算单编号')
            ->setHelp('结算单编号')
            ->hideOnIndex()
        ;

        yield TextField::new('slotId', '广告位ID')
            ->setHelp('产生收入的广告位')
            ->hideOnIndex()
        ;

        yield IntegerField::new('slotSettledRevenue', '广告位结算金额')
            ->setHelp('该广告位结算金额，单位：分')
            ->hideOnIndex()
        ;

        yield TextField::new('mailSendCnt', '补发次数')
            ->setHelp('申请补发结算单次数')
            ->hideOnIndex()
        ;
    }
}
