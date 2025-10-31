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
use WechatOfficialAccountStatsBundle\Entity\MessageSenDistData;

/**
 * 消息发送分布CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/message-send-dist-data', routeName: 'wechat_stats_message_send_dist_data')]
final class MessageSenDistDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageSenDistData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('消息发送分布')
            ->setEntityLabelInPlural('消息发送分布列表')
            ->setPageTitle('index', '消息发送分布统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['countInterval'])
            ->setHelp('index', '微信公众号消息发送分布数据统计')
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
            ->add('countInterval')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
        ;

        yield IntegerField::new('countInterval', '分送区间')
            ->setHelp('发送次数的区间分布')
            ->formatValue(function ($value) {
                if ($value instanceof \BackedEnum) {
                    return method_exists($value, 'getLabel') ? $value->getLabel() : $value->value;
                }

                return $value;
            })
        ;

        yield IntegerField::new('msgUser', '发送用户数')
            ->setHelp('在该区间内发送消息的用户数')
        ;
    }
}
