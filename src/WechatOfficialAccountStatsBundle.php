<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle;

use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use HttpClientBundle\HttpClientBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Tourze\BundleDependency\BundleDependencyInterface;
use WechatOfficialAccountBundle\WechatOfficialAccountBundle;
use Tourze\EasyAdminMenuBundle\EasyAdminMenuBundle;

class WechatOfficialAccountStatsBundle extends Bundle implements BundleDependencyInterface
{
    public static function getBundleDependencies(): array
    {
        return [
            DoctrineBundle::class => ['all' => true],
            HttpClientBundle::class => ['all' => true],
            WechatOfficialAccountBundle::class => ['all' => true],
            EasyAdminMenuBundle::class => ['all' => true],
        ];
    }
}
