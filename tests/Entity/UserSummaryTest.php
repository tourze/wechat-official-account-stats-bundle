<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Entity;

use PHPUnit\Framework\Attributes\CoversClass;
use Tourze\PHPUnitDoctrineEntity\AbstractEntityTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Entity\UserSummary;
use WechatOfficialAccountStatsBundle\Enum\UserSummarySource;

/**
 * @internal
 */
#[CoversClass(UserSummary::class)]
final class UserSummaryTest extends AbstractEntityTestCase
{
    protected function createEntity(): object
    {
        return new UserSummary();
    }

    /**
     * @return iterable<string, array{string, mixed}>
     */
    public static function propertiesProvider(): iterable
    {
        return [
            // 只测试可以安全测试的简单属性
            'newUser' => ['newUser', 100],
            'cancelUser' => ['cancelUser', 50],
        ];
    }

    private UserSummary $userSummary;

    private Account $account;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userSummary = new UserSummary();
        /*
         * 使用具体类 Account 的原因：
         * 1) UserSummary 实体直接依赖 Account 实体类，而不是接口
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
        $this->assertSame(0, $this->userSummary->getId());
    }

    /**
     * 测试账户设置和获取
     */
    public function testSetAndGetAccountWithValidAccountReturnsAccount(): void
    {
        $this->userSummary->setAccount($this->account);

        $this->assertSame($this->account, $this->userSummary->getAccount());
    }

    /**
     * 测试日期设置和获取
     */
    public function testSetAndGetDateWithValidDateReturnsDate(): void
    {
        $date = new \DateTime('2023-01-01');
        $this->userSummary->setDate($date);

        $this->assertSame($date, $this->userSummary->getDate());
    }

    /**
     * 测试默认日期值
     */
    public function testGetDateWithNoValueSetReturnsNull(): void
    {
        $this->assertNull($this->userSummary->getDate());
    }

    /**
     * 测试数据来源设置和获取
     */
    public function testSetAndGetSourceWithValidSourceReturnsSource(): void
    {
        $source = UserSummarySource::SEARCH;
        $this->userSummary->setSource($source);

        $this->assertSame($source, $this->userSummary->getSource());
    }

    /**
     * 测试默认数据来源值
     */
    public function testGetSourceWithNoValueSetReturnsNull(): void
    {
        $this->assertNull($this->userSummary->getSource());
    }

    /**
     * 测试新增用户数设置和获取
     */
    public function testSetAndGetNewUserWithValidValueReturnsValue(): void
    {
        $newUser = 100;
        $this->userSummary->setNewUser($newUser);

        $this->assertSame($newUser, $this->userSummary->getNewUser());
    }

    /**
     * 测试默认新增用户数值
     */
    public function testGetNewUserWithNoValueSetReturnsNull(): void
    {
        $this->assertNull($this->userSummary->getNewUser());
    }

    /**
     * 测试取消关注用户数设置和获取
     */
    public function testSetAndGetCancelUserWithValidValueReturnsValue(): void
    {
        $cancelUser = 50;
        $this->userSummary->setCancelUser($cancelUser);

        $this->assertSame($cancelUser, $this->userSummary->getCancelUser());
    }

    /**
     * 测试默认取消关注用户数值
     */
    public function testGetCancelUserWithNoValueSetReturnsNull(): void
    {
        $this->assertNull($this->userSummary->getCancelUser());
    }

    /**
     * 测试创建时间设置和获取
     */
    public function testSetAndGetCreateTimeWithValidDateTimeReturnsDateTime(): void
    {
        $createTime = new \DateTimeImmutable();
        $this->userSummary->setCreateTime($createTime);

        $this->assertSame($createTime, $this->userSummary->getCreateTime());
    }

    /**
     * 测试默认创建时间值
     */
    public function testGetCreateTimeWithNoValueSetReturnsNull(): void
    {
        $this->assertNull($this->userSummary->getCreateTime());
    }

    /**
     * 测试更新时间设置和获取
     */
    public function testSetAndGetUpdateTimeWithValidDateTimeReturnsDateTime(): void
    {
        $updateTime = new \DateTimeImmutable();
        $this->userSummary->setUpdateTime($updateTime);

        $this->assertSame($updateTime, $this->userSummary->getUpdateTime());
    }

    /**
     * 测试默认更新时间值
     */
    public function testGetUpdateTimeWithNoValueSetReturnsNull(): void
    {
        $this->assertNull($this->userSummary->getUpdateTime());
    }
}
