<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;

class UserSummaryTest extends TestCase
{
    private UserSummary $userSummary;
    private Account $account;

    protected function setUp(): void
    {
        $this->userSummary = new UserSummary();
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsNullByDefault(): void
    {
        $this->assertSame(0, $this->userSummary->getId());
    }

    /**
     * 测试账户设置和获取
     */
    public function testSetAndGetAccount_withValidAccount_returnsAccount(): void
    {
        $result = $this->userSummary->setAccount($this->account);

        $this->assertSame($this->userSummary, $result);
        $this->assertSame($this->account, $this->userSummary->getAccount());
    }

    /**
     * 测试日期设置和获取
     */
    public function testSetAndGetDate_withValidDate_returnsDate(): void
    {
        $date = new DateTime('2023-01-01');
        $result = $this->userSummary->setDate($date);

        $this->assertSame($this->userSummary, $result);
        $this->assertSame($date, $this->userSummary->getDate());
    }

    /**
     * 测试默认日期值
     */
    public function testGetDate_withNoValueSet_returnsNull(): void
    {
        $this->assertNull($this->userSummary->getDate());
    }

    /**
     * 测试数据来源设置和获取
     */
    public function testSetAndGetSource_withValidSource_returnsSource(): void
    {
        $source = UserSummarySource::SEARCH;
        $result = $this->userSummary->setSource($source);

        $this->assertSame($this->userSummary, $result);
        $this->assertSame($source, $this->userSummary->getSource());
    }

    /**
     * 测试默认数据来源值
     */
    public function testGetSource_withNoValueSet_returnsNull(): void
    {
        $this->assertNull($this->userSummary->getSource());
    }

    /**
     * 测试新增用户数设置和获取
     */
    public function testSetAndGetNewUser_withValidValue_returnsValue(): void
    {
        $newUser = 100;
        $result = $this->userSummary->setNewUser($newUser);

        $this->assertSame($this->userSummary, $result);
        $this->assertSame($newUser, $this->userSummary->getNewUser());
    }

    /**
     * 测试默认新增用户数值
     */
    public function testGetNewUser_withNoValueSet_returnsNull(): void
    {
        $this->assertNull($this->userSummary->getNewUser());
    }

    /**
     * 测试取消关注用户数设置和获取
     */
    public function testSetAndGetCancelUser_withValidValue_returnsValue(): void
    {
        $cancelUser = 50;
        $result = $this->userSummary->setCancelUser($cancelUser);

        $this->assertSame($this->userSummary, $result);
        $this->assertSame($cancelUser, $this->userSummary->getCancelUser());
    }

    /**
     * 测试默认取消关注用户数值
     */
    public function testGetCancelUser_withNoValueSet_returnsNull(): void
    {
        $this->assertNull($this->userSummary->getCancelUser());
    }

    /**
     * 测试创建时间设置和获取
     */
    public function testSetAndGetCreateTime_withValidDateTime_returnsDateTime(): void
    {
        $createTime = new DateTimeImmutable();
        $this->userSummary->setCreateTime($createTime);

        $this->assertSame($createTime, $this->userSummary->getCreateTime());
    }

    /**
     * 测试默认创建时间值
     */
    public function testGetCreateTime_withNoValueSet_returnsNull(): void
    {
        $this->assertNull($this->userSummary->getCreateTime());
    }

    /**
     * 测试更新时间设置和获取
     */
    public function testSetAndGetUpdateTime_withValidDateTime_returnsDateTime(): void
    {
        $updateTime = new DateTimeImmutable();
        $this->userSummary->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $this->userSummary->getUpdateTime());
    }

    /**
     * 测试默认更新时间值
     */
    public function testGetUpdateTime_withNoValueSet_returnsNull(): void
    {
        $this->assertNull($this->userSummary->getUpdateTime());
    }
}
