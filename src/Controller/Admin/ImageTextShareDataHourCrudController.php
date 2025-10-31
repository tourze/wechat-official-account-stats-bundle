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
use WechatOfficialAccountStatsBundle\Entity\ImageTextShareDataHour;

/**
 * 图文分享时报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/image-text-share-data-hour', routeName: 'wechat_stats_image_text_share_data_hour')]
final class ImageTextShareDataHourCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ImageTextShareDataHour::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('图文分享时报')
            ->setEntityLabelInPlural('图文分享时报列表')
            ->setPageTitle('index', '图文分享时报')
            ->setDefaultSort(['date' => 'DESC', 'refHour' => 'DESC'])
            ->setSearchFields(['shareScene'])
            ->setHelp('index', '微信公众号图文分享分时数据统计')
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

        yield IntegerField::new('shareScene', '分享场景')
            ->setHelp('分享的场景')
            ->formatValue(function ($value) {
                return $value;
            })
        ;

        yield IntegerField::new('shareCount', '分享次数')
            ->setHelp('分享的次数')
        ;

        yield IntegerField::new('shareUser', '分享人数')
            ->setHelp('分享的人数')
        ;
    }
}
