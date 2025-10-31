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
use WechatOfficialAccountStatsBundle\Entity\UserSummary;

/**
 * 用户概况CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/user-summary', routeName: 'wechat_stats_user_summary')]
final class UserSummaryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return UserSummary::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('用户概况')
            ->setEntityLabelInPlural('用户概况列表')
            ->setPageTitle('index', '微信用户概况统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date', 'source'])
            ->setHelp('index', '微信公众号用户概况数据统计，包括新增、取消关注等数据')
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
            ->add('source')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield TextField::new('source', '数据来源')
            ->hideOnIndex()
        ;

        yield IntegerField::new('newUser', '新增用户数')
            ->setHelp('当日新关注用户数量')
        ;

        yield IntegerField::new('cancelUser', '取消关注用户数')
            ->setHelp('当日取消关注用户数量')
        ;

        yield IntegerField::new('cumulateUser', '累计用户数')
            ->setHelp('截至当日总关注用户数量')
        ;
    }
}
