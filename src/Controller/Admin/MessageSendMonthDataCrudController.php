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
use WechatOfficialAccountStatsBundle\Entity\MessageSendMonthData;

/**
 * 消息发送月报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/message-send-month-data', routeName: 'wechat_stats_message_send_month_data')]
final class MessageSendMonthDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageSendMonthData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('消息发送月报')
            ->setEntityLabelInPlural('消息发送月报列表')
            ->setPageTitle('index', '消息发送月报统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['msgType'])
            ->setHelp('index', '微信公众号消息发送月度数据统计')
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
            ->add('msgType')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield DateField::new('date', '统计日期')
            ->setFormat('yyyy-MM-dd')
            ->setHelp('统计月的起始日期')
        ;

        yield IntegerField::new('msgType', '消息类型')
            ->setHelp('消息类型枚举值')
            ->formatValue(function ($value) {
                if ($value instanceof \BackedEnum) {
                    return method_exists($value, 'getLabel') ? $value->getLabel() : $value->value;
                }

                return $value;
            })
        ;

        yield IntegerField::new('msgUser', '发送用户数')
            ->setHelp('上行发送了（向公众号发送了）消息的用户数')
        ;

        yield IntegerField::new('msgCount', '发送消息总数')
            ->setHelp('上行发送了消息的消息总数')
        ;
    }
}
