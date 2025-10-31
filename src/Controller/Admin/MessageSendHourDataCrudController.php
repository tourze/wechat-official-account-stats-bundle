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
use WechatOfficialAccountStatsBundle\Entity\MessageSendHourData;

/**
 * 消息发送时报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/message-send-hour-data', routeName: 'wechat_stats_message_send_hour_data')]
final class MessageSendHourDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageSendHourData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('消息发送时报')
            ->setEntityLabelInPlural('消息发送时报列表')
            ->setPageTitle('index', '消息发送时报统计')
            ->setDefaultSort(['date' => 'DESC', 'refHour' => 'DESC'])
            ->setSearchFields(['date', 'msgType'])
            ->setHelp('index', '微信公众号消息发送分时数据统计')
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
