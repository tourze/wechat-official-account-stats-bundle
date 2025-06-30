<?php

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

class AdvertisingSpaceDataTest extends TestCase
{
    private AdvertisingSpaceData $advertisingSpaceData;
    private Account $account;

    protected function setUp(): void
    {
        $this->advertisingSpaceData = new AdvertisingSpaceData();
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试默认ID值
     */
    public function testGetId_returnsNullByDefault(): void
    {
        $this->assertSame(0, $this->advertisingSpaceData->getId());
    }

    /**
     * 测试账户设置和获取
     */
    public function testSetAndGetAccount_withValidAccount_returnsAccount(): void
    {
        $result = $this->advertisingSpaceData->setAccount($this->account);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($this->account, $this->advertisingSpaceData->getAccount());
    }

    /**
     * 测试日期设置和获取
     */
    public function testSetAndGetDate_withValidDate_returnsDate(): void
    {
        $date = new DateTimeImmutable('2023-06-15');
        $result = $this->advertisingSpaceData->setDate($date);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($date, $this->advertisingSpaceData->getDate());
    }

    /**
     * 测试广告位ID设置和获取
     */
    public function testSetAndGetSlotId_withValidValue_returnsValue(): void
    {
        $slotId = 123;
        $result = $this->advertisingSpaceData->setSlotId($slotId);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($slotId, $this->advertisingSpaceData->getSlotId());
    }

    /**
     * 测试广告位名称设置和获取
     */
    public function testSetAndGetAdSlot_withValidValue_returnsValue(): void
    {
        $adSlot = '底部广告位';
        $result = $this->advertisingSpaceData->setAdSlot($adSlot);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($adSlot, $this->advertisingSpaceData->getAdSlot());
    }

    /**
     * 测试拉取量设置和获取
     */
    public function testSetAndGetReqSuccCount_withValidValue_returnsValue(): void
    {
        $reqSuccCount = 1000;
        $result = $this->advertisingSpaceData->setReqSuccCount($reqSuccCount);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($reqSuccCount, $this->advertisingSpaceData->getReqSuccCount());
    }

    /**
     * 测试曝光量设置和获取
     */
    public function testSetAndGetExposureCount_withValidValue_returnsValue(): void
    {
        $exposureCount = 800;
        $result = $this->advertisingSpaceData->setExposureCount($exposureCount);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($exposureCount, $this->advertisingSpaceData->getExposureCount());
    }

    /**
     * 测试曝光率设置和获取
     */
    public function testSetAndGetExposureRate_withValidValue_returnsValue(): void
    {
        $exposureRate = '0.8';
        $result = $this->advertisingSpaceData->setExposureRate($exposureRate);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($exposureRate, $this->advertisingSpaceData->getExposureRate());
    }

    /**
     * 测试点击量设置和获取
     */
    public function testSetAndGetClickCount_withValidValue_returnsValue(): void
    {
        $clickCount = 50;
        $result = $this->advertisingSpaceData->setClickCount($clickCount);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($clickCount, $this->advertisingSpaceData->getClickCount());
    }

    /**
     * 测试点击率设置和获取
     */
    public function testSetAndGetClickRate_withValidValue_returnsValue(): void
    {
        $clickRate = '0.0625';
        $result = $this->advertisingSpaceData->setClickRate($clickRate);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($clickRate, $this->advertisingSpaceData->getClickRate());
    }

    /**
     * 测试收入设置和获取
     */
    public function testSetAndGetIncome_withValidValue_returnsValue(): void
    {
        $income = 12345;
        $result = $this->advertisingSpaceData->setIncome($income);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($income, $this->advertisingSpaceData->getIncome());
    }

    /**
     * 测试ECPM设置和获取
     */
    public function testSetAndGetEcpm_withValidValue_returnsValue(): void
    {
        $ecpm = '154.31';
        $result = $this->advertisingSpaceData->setEcpm($ecpm);

        $this->assertSame($this->advertisingSpaceData, $result);
        $this->assertSame($ecpm, $this->advertisingSpaceData->getEcpm());
    }

    /**
     * 测试默认值
     */
    public function testDefaultValues(): void
    {
        $this->assertNull($this->advertisingSpaceData->getDate());
        $this->assertNull($this->advertisingSpaceData->getSlotId());
        $this->assertNull($this->advertisingSpaceData->getAdSlot());
        $this->assertNull($this->advertisingSpaceData->getReqSuccCount());
        $this->assertNull($this->advertisingSpaceData->getExposureCount());
        $this->assertNull($this->advertisingSpaceData->getExposureRate());
        $this->assertNull($this->advertisingSpaceData->getClickCount());
        $this->assertNull($this->advertisingSpaceData->getClickRate());
        $this->assertNull($this->advertisingSpaceData->getIncome());
        $this->assertNull($this->advertisingSpaceData->getEcpm());
    }

    /**
     * 测试toString方法
     */
    public function testToString_returnsIdAsString(): void
    {
        $this->assertSame('0', (string) $this->advertisingSpaceData);
    }

    /**
     * 测试创建时间设置和获取
     */
    public function testSetAndGetCreateTime_withValidDateTime_returnsDateTime(): void
    {
        $createTime = new DateTimeImmutable();
        $this->advertisingSpaceData->setCreateTime($createTime);

        $this->assertSame($createTime, $this->advertisingSpaceData->getCreateTime());
    }

    /**
     * 测试更新时间设置和获取
     */
    public function testSetAndGetUpdateTime_withValidDateTime_returnsDateTime(): void
    {
        $updateTime = new DateTimeImmutable();
        $this->advertisingSpaceData->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $this->advertisingSpaceData->getUpdateTime());
    }
}