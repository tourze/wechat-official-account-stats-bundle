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
use WechatOfficialAccountStatsBundle\Entity\ArticleDailySummary;

/**
 * 文章概况CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/article-daily-summary', routeName: 'wechat_stats_article_daily_summary')]
final class ArticleDailySummaryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ArticleDailySummary::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('文章概况')
            ->setEntityLabelInPlural('文章概况列表')
            ->setPageTitle('index', '文章概况统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['title'])
            ->setHelp('index', '微信公众号文章概况数据统计')
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
            ->add('title')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield TextField::new('title', '文章标题')
            ->setMaxLength(100)
        ;

        yield TextField::new('msgId', '消息ID')
            ->hideOnIndex()
            ->setMaxLength(60)
        ;

        yield IntegerField::new('intPageReadUser', '图文页阅读人数')
            ->setHelp('图文页的阅读人数')
        ;

        yield IntegerField::new('intPageReadCount', '图文页阅读次数')
            ->setHelp('图文页的阅读次数')
        ;

        yield IntegerField::new('oriPageReadUser', '原文页阅读人数')
            ->setHelp('原文页的阅读人数')
        ;

        yield IntegerField::new('oriPageReadCount', '原文页阅读次数')
            ->setHelp('原文页的阅读次数')
        ;

        yield IntegerField::new('shareUser', '分享人数')
            ->setHelp('分享的人数')
        ;

        yield IntegerField::new('shareCount', '分享次数')
            ->setHelp('分享的次数')
        ;

        yield IntegerField::new('addToFavUser', '收藏人数')
            ->setHelp('收藏的人数')
        ;

        yield IntegerField::new('addToFavCount', '收藏次数')
            ->setHelp('收藏的次数')
        ;
    }
}
