<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MessageSendDataTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case TEXT = 1;
    case IMAGE = 2;
    case AUDIO = 3;
    case VIDEO = 4;
    case THIRD_PARTY_APP_MESSAGE = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::TEXT => '文字',
            self::IMAGE => '图片',
            self::AUDIO => '语音',
            self::VIDEO => '视频',
            self::THIRD_PARTY_APP_MESSAGE => '第三方应用消息',
        };
    }
}
