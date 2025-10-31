<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\AdvertisingSpaceData;

/**
 * @internal
 */
#[CoversClass(AdvertisingSpaceData::class)]
final class AdvertisingSpaceDataTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new AdvertisingSpaceData();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'slotId' => ['slotId', 123],
            'adSlot' => ['adSlot', '测试广告位'],
            'reqSuccCount' => ['reqSuccCount', 1000],
            'exposureCount' => ['exposureCount', 800],
            'clickCount' => ['clickCount', 50],
            'income' => ['income', 12345],
        ];
    }

    private AdvertisingSpaceData $advertisingSpaceData;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->advertisingSpaceData = new AdvertisingSpaceData();
        /*
         * 使用具体类 Account 的原因：
         * 1) AdvertisingSpaceData 实体直接依赖 Account 实体类，而不是接口
         * 2) 在单元测试中需要模拟 Account 对象的行为进行测试
         * 3) Account 是 Doctrine 实体类，没有对应的接口抽象
         * 这种使用方式在测试 Doctrine 实体关联时是合理且必要的
         */
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试默认ID值
     */
    public function testGetIdReturnsNullByDefault(): void
    {
        $this->assertSame(0, $this->advertisingSpaceData->getId());
    }

    /**
     * 测试账户设置和获取
     */
    public function testSetAndGetAccountWithValidAccountReturnsAccount(): void
    {
        $this->advertisingSpaceData->setAccount($this->account);

        $this->assertSame($this->account, $this->advertisingSpaceData->getAccount());
    }

    /**
     * 测试日期设置和获取
     */
    public function testSetAndGetDateWithValidDateReturnsDate(): void
    {
        $date = new \DateTimeImmutable('2023-06-15');
        $this->advertisingSpaceData->setDate($date);

        $this->assertSame($date, $this->advertisingSpaceData->getDate());
    }

    /**
     * 测试广告位ID设置和获取
     */
    public function testSetAndGetSlotIdWithValidValueReturnsValue(): void
    {
        $slotId = 123;
        $this->advertisingSpaceData->setSlotId($slotId);

        $this->assertSame($slotId, $this->advertisingSpaceData->getSlotId());
    }

    /**
     * 测试广告位名称设置和获取
     */
    public function testSetAndGetAdSlotWithValidValueReturnsValue(): void
    {
        $adSlot = '底部广告位';
        $this->advertisingSpaceData->setAdSlot($adSlot);

        $this->assertSame($adSlot, $this->advertisingSpaceData->getAdSlot());
    }

    /**
     * 测试拉取量设置和获取
     */
    public function testSetAndGetReqSuccCountWithValidValueReturnsValue(): void
    {
        $reqSuccCount = 1000;
        $this->advertisingSpaceData->setReqSuccCount($reqSuccCount);

        $this->assertSame($reqSuccCount, $this->advertisingSpaceData->getReqSuccCount());
    }

    /**
     * 测试曝光量设置和获取
     */
    public function testSetAndGetExposureCountWithValidValueReturnsValue(): void
    {
        $exposureCount = 800;
        $this->advertisingSpaceData->setExposureCount($exposureCount);

        $this->assertSame($exposureCount, $this->advertisingSpaceData->getExposureCount());
    }

    /**
     * 测试曝光率设置和获取
     */
    public function testSetAndGetExposureRateWithValidValueReturnsValue(): void
    {
        $exposureRate = '0.8';
        $this->advertisingSpaceData->setExposureRate($exposureRate);

        $this->assertSame($exposureRate, $this->advertisingSpaceData->getExposureRate());
    }

    /**
     * 测试点击量设置和获取
     */
    public function testSetAndGetClickCountWithValidValueReturnsValue(): void
    {
        $clickCount = 50;
        $this->advertisingSpaceData->setClickCount($clickCount);

        $this->assertSame($clickCount, $this->advertisingSpaceData->getClickCount());
    }

    /**
     * 测试点击率设置和获取
     */
    public function testSetAndGetClickRateWithValidValueReturnsValue(): void
    {
        $clickRate = '0.0625';
        $this->advertisingSpaceData->setClickRate($clickRate);

        $this->assertSame($clickRate, $this->advertisingSpaceData->getClickRate());
    }

    /**
     * 测试收入设置和获取
     */
    public function testSetAndGetIncomeWithValidValueReturnsValue(): void
    {
        $income = 12345;
        $this->advertisingSpaceData->setIncome($income);

        $this->assertSame($income, $this->advertisingSpaceData->getIncome());
    }

    /**
     * 测试ECPM设置和获取
     */
    public function testSetAndGetEcpmWithValidValueReturnsValue(): void
    {
        $ecpm = '154.31';
        $this->advertisingSpaceData->setEcpm($ecpm);

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
    public function testToStringReturnsIdAsString(): void
    {
        $this->assertSame('0', (string) $this->advertisingSpaceData);
    }

    /**
     * 测试创建时间设置和获取
     */
    public function testSetAndGetCreateTimeWithValidDateTimeReturnsDateTime(): void
    {
        $createTime = new \DateTimeImmutable();
        $this->advertisingSpaceData->setCreateTime($createTime);

        $this->assertSame($createTime, $this->advertisingSpaceData->getCreateTime());
    }

    /**
     * 测试更新时间设置和获取
     */
    public function testSetAndGetUpdateTimeWithValidDateTimeReturnsDateTime(): void
    {
        $updateTime = new \DateTimeImmutable();
        $this->advertisingSpaceData->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $this->advertisingSpaceData->getUpdateTime());
    }
}
