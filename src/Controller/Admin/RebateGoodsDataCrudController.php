<?php

namespace WechatOfficialAccountStatsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use WechatOfficialAccountStatsBundle\Entity\RebateGoodsData;

/**
 * 返佣商品数据CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/rebate-goods-data', routeName: 'wechat_stats_rebate_goods_data')]
final class RebateGoodsDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return RebateGoodsData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('返佣商品数据')
            ->setEntityLabelInPlural('返佣商品数据列表')
            ->setPageTitle('index', '返佣商品数据统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date'])
            ->setHelp('index', '微信公众号返佣商品数据统计')
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
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield IntegerField::new('exposureCount', '曝光量')
            ->setHelp('商品曝光量')
        ;

        yield IntegerField::new('clickCount', '点击量')
            ->setHelp('商品点击量')
        ;

        yield TextField::new('clickRate', '点击率')
            ->setHelp('商品点击率')
        ;

        yield IntegerField::new('orderCount', '订单量')
            ->setHelp('商品订单量')
        ;

        yield TextField::new('orderRate', '下单率')
            ->setHelp('商品下单率')
        ;

        yield IntegerField::new('totalFee', '订单金额(分)')
            ->setHelp('订单总金额，单位：分')
        ;

        yield IntegerField::new('totalCommission', '预估收入(分)')
            ->setHelp('预估返佣收入，单位：分')
        ;
    }
}
