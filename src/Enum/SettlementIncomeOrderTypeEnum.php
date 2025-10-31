<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SettlementIncomeOrderTypeEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case FIRST_HALF_OF_MONTH = 1;
    case SECOND_HALF_OF_MONTH = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::FIRST_HALF_OF_MONTH => '上半月',
            self::SECOND_HALF_OF_MONTH => '下半月',
        };
    }
}
