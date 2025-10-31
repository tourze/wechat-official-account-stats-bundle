# 微信公众号统计数据包

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-official-account-stats-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-official-account-stats-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![PHP Version Require](https://poser.pugx.org/tourze/wechat-official-account-stats-bundle/require/php?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-official-account-stats-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo/main.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

一个用于同步和管理微信公众号统计数据的 Symfony 包，包括用户分析、图文分析、消息分析和广告分析等功能。

## 目录

- [状态](#状态)
- [功能特性](#功能特性)
- [系统要求](#系统要求)
- [安装](#安装)
- [快速开始](#快速开始)
  - [1. 注册包](#1-注册包)
  - [2. 同步数据](#2-同步数据)
  - [3. 访问数据](#3-访问数据)
- [使用方法](#使用方法)
  - [可用命令](#可用命令)
  - [手动执行](#手动执行)
  - [自动调度](#自动调度)
- [实体类](#实体类)
- [测试](#测试)
- [仓储类](#仓储类)
- [贡献指南](#贡献指南)
- [许可证](#许可证)

## 状态

- ✅ **PHPStan 级别 5**: 静态分析通过
- ✅ **单元测试**: 201个测试，353个断言
- ✅ **包检查**: 所有验证通过
- ✅ **Command测试**: 所有命令测试使用CommandTester

## 功能特性

- 自动同步微信公众号统计数据
- 内置定时任务，定期更新数据
- 全面的数据覆盖，包括：
  - 用户增长和累计统计
  - 图文阅读、分享、收藏统计
  - 消息发送统计和分布
  - 广告位收入和返佣数据
- 为所有统计数据提供实体和仓储支持
- 内置 cron 调度，自动同步数据

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0 或更高版本
- 微信公众号基础包

## 安装

使用 Composer 安装此包：

```bash
composer require tourze/wechat-official-account-stats-bundle
```

## 快速开始

### 1. 注册包

在 `config/bundles.php` 中添加：

```php
return [
    // ...
    WechatOfficialAccountStatsBundle\WechatOfficialAccountStatsBundle::class => ['all' => true],
];
```

### 2. 同步数据

运行同步命令开始收集统计数据：

```bash
# 同步用户增长数据
php bin/console wechat:official-account:sync-user-summary

# 同步图文统计数据
php bin/console wechat:official-account:sync-article-summary
```

### 3. 访问数据

使用仓储类访问同步的数据：

```php
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

class AnalyticsService
{
    public function __construct(
        private UserSummaryRepository $userSummaryRepository
    ) {}

    public function getUserGrowth(\DateTime $date): array
    {
        return $this->userSummaryRepository->findBy(['date' => $date]);
    }
}
```

## 使用方法

### 可用命令

此包提供以下控制台命令用于数据同步：

#### 用户分析命令

- `wechat:official-account:sync-user-summary` - 同步用户增减数据（按来源统计的新增用户、取消关注用户）
- `wechat:official-account:sync-user-cumulate` - 同步累计用户数据

#### 图文分析命令

- `wechat:official-account:sync-article-summary` - 同步图文群发每日数据（阅读、分享、收藏）
- `wechat:official-account:sync-article-total` - 同步图文群发总数据（含渠道分布）
- `wechat:official-account:sync-image-text-statistics` - 同步图文统计数据
- `wechat:official-account:sync-image-text-statistics-hour` - 同步图文统计分时数据
- `wechat:official-account:sync-image-text-share-data` - 同步图文分享转发数据
- `wechat:official-account:sync-image-text-share-data-hour` - 同步图文分享转发分时数据

#### 消息分析命令

- `wechat:official-account:sync-message-send-data` - 同步消息发送概况数据
- `wechat:official-account:sync-message-send-hour-data` - 同步消息发送分时数据
- `wechat:official-account:sync-message-send-week-data` - 同步消息发送周数据
- `wechat:official-account:sync-message-send-month-data` - 同步消息发送月数据
- `wechat:official-account:sync-message-send-dist-data` - 同步消息发送分布数据

#### 广告分析命令

- `wechat:official-account:sync-advertising-space-data` - 同步公众号分广告位数据
- `wechat:official-account:sync-rebate-goods-data` - 同步公众号返佣商品数据
- `wechat:official-account:sync-settlement-income` - 同步公众号结算收入数据

#### 接口分析命令（当前已禁用）

- `wechat:official-account:sync-interface-summary` - 同步接口分析数据
- `wechat:official-account:sync-interface-summary-hour` - 同步接口分析分时数据

### 手动执行

您可以手动执行任何命令：

```bash
php bin/console wechat:official-account:sync-user-summary
```

### 自动调度

大多数命令都配置了 cron 表达式用于自动执行。例如：
- 用户数据每天凌晨 3:04 同步
- 图文数据每天中午 12:00 同步
- 消息数据在一天中的不同时间同步

要启用自动调度，请确保您的 cron 任务包已正确配置。

## 实体类

此包提供以下实体类用于存储统计数据：

- `UserSummary` - 用户增减统计
- `UserCumulate` - 累计用户数
- `ArticleDailySummary` - 图文群发每日数据
- `ArticleTotal` - 图文群发总数据
- `ImageTextStatistics` - 图文统计数据
- `ImageTextStatisticsHour` - 图文统计分时数据
- `ImageTextShareData` - 图文分享转发数据
- `ImageTextShareDataHour` - 图文分享转发分时数据
- `MessageSendData` - 消息发送概况数据
- `MessageSendHourData` - 消息发送分时数据
- `MessageSendWeekData` - 消息发送周数据
- `MessageSendMonthData` - 消息发送月数据
- `MessageSenDistData` - 消息发送分布数据
- `AdvertisingSpaceData` - 广告位数据
- `RebateGoodsData` - 返佣商品数据
- `SettlementIncomeData` - 结算收入数据
- `InterfaceSummary` - 接口分析数据
- `InterfaceSummaryHour` - 接口分析分时数据

## 测试

运行包测试：

```bash
# 运行所有测试
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests

# 运行特定测试套件
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests/Entity/
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests/Enum/
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests/Request/

# 运行 PHPStan 分析
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-official-account-stats-bundle
```

## 仓储类

每个实体都有对应的仓储类用于数据访问：

```php
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

// 注入仓储
public function __construct(private UserSummaryRepository $userSummaryRepository) {}

// 查找用户统计数据
$summary = $this->userSummaryRepository->findOneBy([
    'account' => $account,
    'date' => $date,
    'source' => $source,
]);
```

## 贡献指南

详情请参阅 [CONTRIBUTING.md](CONTRIBUTING.md)。

## 许可证

MIT 许可证（MIT）。详情请参阅[许可证文件](LICENSE)。