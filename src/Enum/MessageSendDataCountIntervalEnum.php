<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum MessageSendDataCountIntervalEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case ZERO = 0;
    case ONE_TO_FIVE = 1;
    case SIX_TO_TEN = 2;
    case MORE_THAN_TEN = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::ZERO => '0',
            self::ONE_TO_FIVE => '1-5',
            self::SIX_TO_TEN => '6-10',
            self::MORE_THAN_TEN => '10次以上',
        };
    }
}
