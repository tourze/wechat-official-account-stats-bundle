<?php

namespace WechatOfficialAccountStatsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

enum SettlementIncomeOrderStatusEnum: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case SETTLING = 1;
    case SETTLED = 2;
    case SETTLED_TWO = 3;
    case PAYMENT_PENDING = 4;
    case PAID = 5;

    public function getLabel(): string
    {
        return match ($this) {
            self::SETTLING => '结算中',
            self::SETTLED => '已结算',
            self::SETTLED_TWO => '已结算',
            self::PAYMENT_PENDING => '付款中',
            self::PAID => '已付款',
        };
    }
}
