# WeChat Official Account Stats Bundle

[English](README.md) | [中文](README.zh-CN.md)

[![Latest Version](https://img.shields.io/packagist/v/tourze/wechat-official-account-stats-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/wechat-official-account-stats-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![PHP Version Require](https://poser.pugx.org/tourze/wechat-official-account-stats-bundle/require/php?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![License](https://img.shields.io/packagist/l/tourze/wechat-official-account-stats-bundle.svg?style=flat-square)](https://packagist.org/packages/tourze/wechat-official-account-stats-bundle)
[![Coverage Status](https://img.shields.io/codecov/c/github/tourze/php-monorepo/main.svg?style=flat-square)](https://codecov.io/gh/tourze/php-monorepo)

A Symfony bundle for synchronizing and managing WeChat Official Account statistics data, including user analytics, article analytics, message analytics, and advertising analytics.

## Table of Contents

- [Status](#status)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Quick Start](#quick-start)
  - [1. Register the Bundle](#1-register-the-bundle)
  - [2. Sync Data](#2-sync-data)
  - [3. Access Data](#3-access-data)
- [Usage](#usage)
  - [Available Commands](#available-commands)
  - [Manual Execution](#manual-execution)
  - [Automatic Scheduling](#automatic-scheduling)
- [Entities](#entities)
- [Testing](#testing)
- [Repository Classes](#repository-classes)
- [Contributing](#contributing)
- [License](#license)

## Status

- ✅ **PHPStan Level 5**: Static analysis passed
- ✅ **Unit Tests**: 201 tests, 353 assertions
- ✅ **Package Check**: All validation passed
- ✅ **Command Tests**: All command tests with CommandTester

## Features

- Automatic synchronization of WeChat Official Account statistics data
- Scheduled tasks for regular data updates
- Comprehensive data coverage including:
  - User growth and cumulative statistics
  - Article reading, sharing, and favorites statistics
  - Message sending statistics and distribution
  - Advertising space revenue and commission data
- Entity and repository support for all statistics data
- Built-in cron scheduling for automatic data synchronization

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0 or higher
- WeChat Official Account Bundle

## Installation

Install the bundle using Composer:

```bash
composer require tourze/wechat-official-account-stats-bundle
```

## Quick Start

### 1. Register the Bundle

Add to `config/bundles.php`:

```php
return [
    // ...
    WechatOfficialAccountStatsBundle\WechatOfficialAccountStatsBundle::class => ['all' => true],
];
```

### 2. Sync Data

Run a sync command to start collecting statistics:

```bash
# Sync user growth data
php bin/console wechat:official-account:sync-user-summary

# Sync article statistics
php bin/console wechat:official-account:sync-article-summary
```

### 3. Access Data

Use repository classes to access the synchronized data:

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

## Usage

### Available Commands

The bundle provides the following console commands for data synchronization:

#### User Analytics Commands

- `wechat:official-account:sync-user-summary` - Sync user growth data (new users, unsubscribed users by source)
- `wechat:official-account:sync-user-cumulate` - Sync cumulative user count data

#### Article Analytics Commands

- `wechat:official-account:sync-article-summary` - Sync daily article statistics (reads, shares, favorites)
- `wechat:official-account:sync-article-total` - Sync total article statistics with channel breakdown
- `wechat:official-account:sync-image-text-statistics` - Sync article read and share statistics
- `wechat:official-account:sync-image-text-statistics-hour` - Sync hourly article statistics
- `wechat:official-account:sync-image-text-share-data` - Sync article share and forward data
- `wechat:official-account:sync-image-text-share-data-hour` - Sync hourly article share data

#### Message Analytics Commands

- `wechat:official-account:sync-message-send-data` - Sync message sending overview data
- `wechat:official-account:sync-message-send-hour-data` - Sync hourly message sending data
- `wechat:official-account:sync-message-send-week-data` - Sync weekly message sending data
- `wechat:official-account:sync-message-send-month-data` - Sync monthly message sending data
- `wechat:official-account:sync-message-send-dist-data` - Sync message distribution data

#### Advertising Analytics Commands

- `wechat:official-account:sync-advertising-space-data` - Sync advertising space revenue data
- `wechat:official-account:sync-rebate-goods-data` - Sync commission product data
- `wechat:official-account:sync-settlement-income` - Sync settlement income data

#### Interface Analytics Commands (Currently Disabled)

- `wechat:official-account:sync-interface-summary` - Sync API call statistics
- `wechat:official-account:sync-interface-summary-hour` - Sync hourly API call statistics

### Manual Execution

You can manually execute any command:

```bash
php bin/console wechat:official-account:sync-user-summary
```

### Automatic Scheduling

Most commands are configured with cron expressions for automatic execution. For example:
- User data syncs daily at 3:04 AM
- Article data syncs daily at 12:00 PM
- Message data syncs at various times throughout the day

To enable automatic scheduling, ensure your cron job bundle is properly configured.

## Entities

The bundle provides the following entities for storing statistics data:

- `UserSummary` - User growth statistics
- `UserCumulate` - Cumulative user counts
- `ArticleDailySummary` - Daily article statistics
- `ArticleTotal` - Total article statistics
- `ImageTextStatistics` - Article read and share statistics
- `ImageTextStatisticsHour` - Hourly article statistics
- `ImageTextShareData` - Article share data
- `ImageTextShareDataHour` - Hourly article share data
- `MessageSendData` - Message sending statistics
- `MessageSendHourData` - Hourly message statistics
- `MessageSendWeekData` - Weekly message statistics
- `MessageSendMonthData` - Monthly message statistics
- `MessageSenDistData` - Message distribution data
- `AdvertisingSpaceData` - Advertising space revenue data
- `RebateGoodsData` - Commission product data
- `SettlementIncomeData` - Settlement income data
- `InterfaceSummary` - API call statistics
- `InterfaceSummaryHour` - Hourly API call statistics

## Testing

To run the package tests:

```bash
# Run all tests
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests

# Run specific test suites
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests/Entity/
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests/Enum/
./vendor/bin/phpunit packages/wechat-official-account-stats-bundle/tests/Request/

# Run PHPStan analysis
php -d memory_limit=2G ./vendor/bin/phpstan analyse packages/wechat-official-account-stats-bundle
```

## Repository Classes

Each entity has a corresponding repository class for data access:

```php
use WechatOfficialAccountStatsBundle\Repository\UserSummaryRepository;

// Inject the repository
public function __construct(private UserSummaryRepository $userSummaryRepository) {}

// Find user summary data
$summary = $this->userSummaryRepository->findOneBy([
    'account' => $account,
    'date' => $date,
    'source' => $source,
]);
```

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.