<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Enum;

use Tourze\EnumExtra\Itemable;
use Tourze\EnumExtra\ItemTrait;
use Tourze\EnumExtra\Labelable;
use Tourze\EnumExtra\Selectable;
use Tourze\EnumExtra\SelectTrait;

/**
 * @see https://developers.weixin.qq.com/doc/offiaccount/Analytics/User_Analysis_Data_Interface.html
 */
enum UserSummarySource: int implements Labelable, Itemable, Selectable
{
    use ItemTrait;
    use SelectTrait;

    case OTHER = 0;
    case SEARCH = 1;
    case CARD = 17;
    case SCAN_QRCODE = 30;
    case ARTICLE_ACCOUNT = 57;
    case AD = 100;
    case REPRINT = 161;
    case MP = 149;
    case VIDEO = 200;
    case LIVE = 201;

    public function getLabel(): string
    {
        return match ($this) {
            self::OTHER => '其他合计',
            self::SEARCH => '公众号搜索',
            self::CARD => '名片分享',
            self::SCAN_QRCODE => '扫描二维码',
            self::ARTICLE_ACCOUNT => '文章内账号名称',
            self::AD => '微信广告',
            self::REPRINT => '他人转载',
            self::MP => '小程序关注',
            self::VIDEO => '视频号',
            self::LIVE => '直播',
        };
    }
}
