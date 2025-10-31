<?php

namespace WechatOfficialAccountStatsBundle\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminCrud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use WechatOfficialAccountStatsBundle\Entity\MessageSendData;
use WechatOfficialAccountStatsBundle\Enum\MessageSendDataTypeEnum;

/**
 * 消息发送日报CRUD控制器
 */
#[AdminCrud(routePath: '/wechat-stats/message-send-data', routeName: 'wechat_stats_message_send_data')]
final class MessageSendDataCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MessageSendData::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('消息发送日报')
            ->setEntityLabelInPlural('消息发送日报列表')
            ->setPageTitle('index', '消息发送日报统计')
            ->setDefaultSort(['date' => 'DESC'])
            ->setSearchFields(['date'])
            ->setHelp('index', '微信公众号消息发送日报数据统计')
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
        ;

        yield ChoiceField::new('msgType', '消息类型')
            ->setChoices(MessageSendDataTypeEnum::cases())
            ->setHelp('消息类型：文字、图片、语音、视频、第三方应用消息')
            ->formatValue(function ($value) {
                if ($value instanceof \BackedEnum) {
                    return method_exists($value, 'getLabel') ? $value->getLabel() : $value->value;
                }

                return $value;
            })
        ;

        yield IntegerField::new('msgUser', '发送用户数')
            ->setHelp('上行发送了消息的用户数')
        ;

        yield IntegerField::new('msgCount', '消息总数')
            ->setHelp('上行发送了消息的消息总数')
        ;
    }
}
