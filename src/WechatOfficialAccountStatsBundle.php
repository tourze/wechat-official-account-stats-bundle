<?php

namespace WechatOfficialAccountStatsBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\EasyAdmin\Attribute\Permission\AsPermission;

#[AsPermission(title: '数据统计')]
class WechatOfficialAccountStatsBundle extends Bundle
{
}
