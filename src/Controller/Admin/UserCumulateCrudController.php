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
use WechatOfficialAccountStatsBundle\Entity\UserCumulate;

/**
 * 用户累计CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/user-cumulate', routeName: 'wechat_stats_user_cumulate')]
final class UserCumulateCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserCumulate::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('用户累计')
            ->setEntityLabelInPlural('用户累计列表')
            ->setPageTitle('index', '微信用户累计统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date'])
            ->setHelp('index', '微信公众号用户累计数据统计')
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

        yield IntegerField::new('cumulateUser', '累计用户数')
            ->setHelp('截至当日的累计关注用户数')
        ;
    }
}
