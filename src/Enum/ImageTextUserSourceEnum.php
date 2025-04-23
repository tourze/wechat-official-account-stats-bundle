<?php

namespace WechatOfficialAccountStatsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum ImageTextUserSourceEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ALL = 99999999;
    case CONVERSATION = 0;
    case FRIENDS = 1;
    case MOMENTS = 2;
    case HISTORICAL_MESSAGES_PAGE = 4;
    case OTHER = 5;
    case VIEW_AND_DISCOVER = 6;
    case SEARCH = 7;

    public function getLabel(): string
    {
        return match ($this) {
            self::ALL => '全部',
            self::CONVERSATION => '会话',
            self::FRIENDS => '好友',
            self::MOMENTS => '朋友圈',
            self::HISTORICAL_MESSAGES_PAGE => '历史消息页',
            self::OTHER => '其他',
            self::VIEW_AND_DISCOVER => '看一看',
            self::SEARCH => '搜一搜',
        };
    }
}
