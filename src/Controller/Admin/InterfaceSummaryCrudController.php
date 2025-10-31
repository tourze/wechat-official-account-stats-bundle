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
use WechatOfficialAccountStatsBundle\Entity\InterfaceSummary;

/**
 * 接口分析日报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/interface-summary', routeName: 'wechat_stats_interface_summary')]
final class InterfaceSummaryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return InterfaceSummary::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('接口分析日报')
            ->setEntityLabelInPlural('接口分析日报列表')
            ->setPageTitle('index', '接口分析日报')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date'])
            ->setHelp('index', '微信公众号接口分析数据统计')
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
            ->add('callbackCount')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield IntegerField::new('callbackCount', '被动回复次数')
            ->setHelp('通过服务器配置地址获得消息后，被动回复用户消息的次数')
        ;

        yield IntegerField::new('failCount', '失败次数')
            ->setHelp('上述动作的失败次数')
        ;

        yield IntegerField::new('totalTimeCost', '总耗时')
            ->setHelp('总耗时，除以callback_count即为平均耗时')
        ;

        yield IntegerField::new('maxTimeCost', '最大耗时')
            ->setHelp('最大耗时')
        ;
    }
}
