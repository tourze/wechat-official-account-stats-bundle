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
use WechatOfficialAccountStatsBundle\Entity\ImageTextStatistics;

/**
 * 图文统计日报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/image-text-statistics', routeName: 'wechat_stats_image_text_statistics')]
final class ImageTextStatisticsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImageTextStatistics::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('图文统计日报')
            ->setEntityLabelInPlural('图文统计日报列表')
            ->setPageTitle('index', '图文统计日报')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date', 'userSource'])
            ->setHelp('index', '微信公众号图文统计数据')
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
            ->add('userSource')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield IntegerField::new('userSource', '用户来源')
            ->setHelp('用户从哪里进入来阅读该图文')
            ->formatValue(function ($value) {
                if ($value instanceof \BackedEnum) {
                    return method_exists($value, 'getLabel') ? $value->getLabel() : $value->value;
                }

                return $value;
            })
        ;

        yield IntegerField::new('intPageReadUser', '图文页阅读人数')
            ->setHelp('图文页（点击群发图文卡片进入的页面）的阅读人数')
        ;

        yield IntegerField::new('intPageReadCount', '图文页阅读次数')
            ->setHelp('图文页的阅读次数')
        ;

        yield IntegerField::new('oriPageReadUser', '原文页阅读人数')
            ->setHelp('原文页（点击图文页"阅读原文"进入的页面）的阅读人数')
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
