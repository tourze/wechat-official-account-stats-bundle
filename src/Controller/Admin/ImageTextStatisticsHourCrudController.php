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
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatisticsHour;

/**
 * 图文统计时报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/image-text-statistics-hour', routeName: 'wechat_stats_image_text_statistics_hour')]
final class ImageTextStatisticsHourCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImageTextStatisticsHour::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('图文统计时报')
            ->setEntityLabelInPlural('图文统计时报列表')
            ->setPageTitle('index', '图文统计时报')
            ->setDefaultSort(['date' => 'DESC', 'refHour' => 'DESC'])
            ->setSearchFields(['userSource'])
            ->setHelp('index', '微信公众号图文阅读分时数据')
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
            ->add('refHour')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield IntegerField::new('refHour', '小时')
            ->setHelp('统计小时（0-23）')
        ;

        yield IntegerField::new('userSource', '用户来源')
            ->setHelp('用户从哪里进入来阅读该图文')
            ->formatValue(function ($value) {
                return $value;
            })
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
