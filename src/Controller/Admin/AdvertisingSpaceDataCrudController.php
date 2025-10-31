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
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

/**
 * 广告位数据CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/advertising-space-data', routeName: 'wechat_stats_advertising_space_data')]
final class AdvertisingSpaceDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return AdvertisingSpaceData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('广告位数据')
            ->setEntityLabelInPlural('广告位数据列表')
            ->setPageTitle('index', '广告位数据统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date', 'adSlot'])
            ->setHelp('index', '微信公众号分广告位数据统计')
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
            ->add('adSlot')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield IntegerField::new('slotId', '广告位类型ID')
            ->setHelp('广告位类型标识')
        ;

        yield TextField::new('adSlot', '广告位类型名称')
            ->setHelp('广告位类型名称')
        ;

        yield IntegerField::new('reqSuccCount', '拉取量')
            ->setHelp('广告拉取量')
        ;

        yield IntegerField::new('exposureCount', '曝光量')
            ->setHelp('广告曝光量')
        ;

        yield TextField::new('exposureRate', '曝光率')
            ->setHelp('广告曝光率')
        ;

        yield IntegerField::new('clickCount', '点击量')
            ->setHelp('广告点击量')
        ;

        yield TextField::new('clickRate', '点击率')
            ->setHelp('广告点击率')
        ;

        yield IntegerField::new('income', '收入(分)')
            ->setHelp('广告收入，单位：分')
        ;

        yield TextField::new('ecpm', '千次曝光收益(分)')
            ->setHelp('广告千次曝光收益，单位：分')
            ->hideOnIndex()
        ;
    }
}
