<?php

namespace WechatOfficialAccountStatsBundle\Tests\Command;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Command\SyncAdvertisingSpaceDataCommand;

class SyncAdvertisingSpaceDataCommandTest extends TestCase
{
    /**
     * 测试命令类是否存在
     */
    public function testCommandExists(): void
    {
        $this->assertTrue(class_exists(SyncAdvertisingSpaceDataCommand::class));
    }
}