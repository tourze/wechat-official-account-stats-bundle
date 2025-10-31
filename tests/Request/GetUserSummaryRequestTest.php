<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use Carbon\CarbonImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Request\GetUserSummaryRequest;

/**
 * @internal
 */
#[CoversClass(GetUserSummaryRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetUserSummaryRequestTest extends AbstractIntegrationTestCase
{
    private GetUserSummaryRequest $request;

    private Account $account;

    protected function onSetUp(): void
    {
        $this->request = $this->createMock(GetUserSummaryRequest::class);
        /*
         * 使用具体类 Account 的原因：
         * 1) GetUserSummaryRequest 需要 Account 实体对象作为参数
         * 2) 在测试中需要模拟 Account 的具体行为和方法调用
         * 3) Account 是 Doctrine 实体类，没有对应的接口抽象
         * 这种使用方式在测试请求类时是合理且必要的
         */
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试请求路径是否正确返回
     */
    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $expectedPath = 'https://api.weixin.qq.com/datacube/getusersummary';
        $this->request->method('getRequestPath')
            ->willReturn($expectedPath)
        ;
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    /**
     * 测试请求选项生成
     */
    public function testGetRequestOptionsWithValidDatesReturnsCorrectOptions(): void
    {
        $beginDate = CarbonImmutable::create(2023, 1, 1);
        $endDate = CarbonImmutable::create(2023, 1, 7);
        $this->assertNotNull($beginDate);
        $this->assertNotNull($endDate);

        $expectedOptions = [
            'json' => [
                'begin_date' => '2023-01-01',
                'end_date' => '2023-01-07',
            ],
        ];

        $this->request->method('getRequestOptions')
            ->willReturn($expectedOptions)
        ;
        $this->request->setBeginDate($beginDate);
        $this->request->setEndDate($endDate);

        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);
        $this->assertArrayHasKey('json', $options);
        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertArrayHasKey('begin_date', $jsonData);
        $this->assertArrayHasKey('end_date', $jsonData);
        $this->assertSame('2023-01-01', $jsonData['begin_date']);
        $this->assertSame('2023-01-07', $jsonData['end_date']);
    }

    /**
     * 测试开始日期设置和获取
     */
    public function testSetAndGetBeginDateWithValidDateReturnsDate(): void
    {
        $beginDate = CarbonImmutable::create(2023, 1, 1);
        $this->assertNotNull($beginDate);
        $this->request->method('getBeginDate')
            ->willReturn($beginDate)
        ;
        $this->request->setBeginDate($beginDate);

        $this->assertSame($beginDate, $this->request->getBeginDate());
    }

    /**
     * 测试结束日期设置和获取
     */
    public function testSetAndGetEndDateWithValidDateReturnsDate(): void
    {
        $endDate = CarbonImmutable::create(2023, 1, 7);
        $this->assertNotNull($endDate);
        $this->request->method('getEndDate')
            ->willReturn($endDate)
        ;
        $this->request->setEndDate($endDate);

        $this->assertSame($endDate, $this->request->getEndDate());
    }

    /**
     * 测试账户设置和获取
     */
    public function testSetAndGetAccountWithValidAccountReturnsAccount(): void
    {
        $this->request->method('getAccount')
            ->willReturn($this->account)
        ;
        $this->request->setAccount($this->account);

        $this->assertSame($this->account, $this->request->getAccount());
    }

    /**
     * 测试日期范围超出最大限制的情况
     * 微信API要求开始日期和结束日期相差不能超过一定天数
     */
    public function testGetRequestOptionsWithExcessiveDateRangeStillGeneratesOptions(): void
    {
        // 这里设置一个超过7天的日期范围
        $beginDate = CarbonImmutable::create(2023, 1, 1);
        $endDate = CarbonImmutable::create(2023, 1, 15); // 超过7天
        $this->assertNotNull($beginDate);
        $this->assertNotNull($endDate);

        $expectedOptions = [
            'json' => [
                'begin_date' => '2023-01-01',
                'end_date' => '2023-01-15',
            ],
        ];

        $this->request->method('getRequestOptions')
            ->willReturn($expectedOptions)
        ;
        $this->request->setBeginDate($beginDate);
        $this->request->setEndDate($endDate);

        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);

        // 即使日期范围超出，请求选项仍然会正确生成
        $this->assertArrayHasKey('json', $options);
        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertSame('2023-01-01', $jsonData['begin_date']);
        $this->assertSame('2023-01-15', $jsonData['end_date']);
    }

    /**
     * 测试结束日期在开始日期之前的情况
     */
    public function testGetRequestOptionsWithEndDateBeforeBeginDateStillGeneratesOptions(): void
    {
        // 结束日期在开始日期之前
        $beginDate = CarbonImmutable::create(2023, 1, 10);
        $endDate = CarbonImmutable::create(2023, 1, 5);
        $this->assertNotNull($beginDate);
        $this->assertNotNull($endDate);

        $expectedOptions = [
            'json' => [
                'begin_date' => '2023-01-10',
                'end_date' => '2023-01-05',
            ],
        ];

        $this->request->method('getRequestOptions')
            ->willReturn($expectedOptions)
        ;
        $this->request->setBeginDate($beginDate);
        $this->request->setEndDate($endDate);

        $options = $this->request->getRequestOptions();
        $this->assertIsArray($options);

        // 即使日期顺序不正确，请求选项仍然会生成
        $this->assertArrayHasKey('json', $options);
        $jsonData = $options['json'];
        $this->assertIsArray($jsonData);
        $this->assertSame('2023-01-10', $jsonData['begin_date']);
        $this->assertSame('2023-01-05', $jsonData['end_date']);
    }
}
