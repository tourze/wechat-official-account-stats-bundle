<?php

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Request\GetUserSummaryRequest;

class GetUserSummaryRequestTest extends TestCase
{
    private GetUserSummaryRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new GetUserSummaryRequest();
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试请求路径是否正确返回
     */
    public function testGetRequestPath_returnsCorrectPath(): void
    {
        $expectedPath = 'https://api.weixin.qq.com/datacube/getusersummary';
        $this->assertSame($expectedPath, $this->request->getRequestPath());
    }

    /**
     * 测试请求选项生成
     */
    public function testGetRequestOptions_withValidDates_returnsCorrectOptions(): void
    {
        $beginDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::create(2023, 1, 7);

        $this->request->setBeginDate($beginDate);
        $this->request->setEndDate($endDate);

        $options = $this->request->getRequestOptions();
        $this->assertArrayHasKey('json', $options);
        $this->assertArrayHasKey('begin_date', $options['json']);
        $this->assertArrayHasKey('end_date', $options['json']);
        $this->assertSame('2023-01-01', $options['json']['begin_date']);
        $this->assertSame('2023-01-07', $options['json']['end_date']);
    }

    /**
     * 测试开始日期设置和获取
     */
    public function testSetAndGetBeginDate_withValidDate_returnsDate(): void
    {
        $beginDate = Carbon::create(2023, 1, 1);
        $this->request->setBeginDate($beginDate);

        $this->assertSame($beginDate, $this->request->getBeginDate());
    }

    /**
     * 测试结束日期设置和获取
     */
    public function testSetAndGetEndDate_withValidDate_returnsDate(): void
    {
        $endDate = Carbon::create(2023, 1, 7);
        $this->request->setEndDate($endDate);

        $this->assertSame($endDate, $this->request->getEndDate());
    }

    /**
     * 测试账户设置和获取
     */
    public function testSetAndGetAccount_withValidAccount_returnsAccount(): void
    {
        $this->request->setAccount($this->account);

        $this->assertSame($this->account, $this->request->getAccount());
    }

    /**
     * 测试日期范围超出最大限制的情况
     * 微信API要求开始日期和结束日期相差不能超过一定天数
     */
    public function testGetRequestOptions_withExcessiveDateRange_stillGeneratesOptions(): void
    {
        // 这里设置一个超过7天的日期范围
        $beginDate = Carbon::create(2023, 1, 1);
        $endDate = Carbon::create(2023, 1, 15); // 超过7天

        $this->request->setBeginDate($beginDate);
        $this->request->setEndDate($endDate);

        $options = $this->request->getRequestOptions();

        // 即使日期范围超出，请求选项仍然会正确生成
        $this->assertArrayHasKey('json', $options);
        $this->assertSame('2023-01-01', $options['json']['begin_date']);
        $this->assertSame('2023-01-15', $options['json']['end_date']);
    }

    /**
     * 测试结束日期在开始日期之前的情况
     */
    public function testGetRequestOptions_withEndDateBeforeBeginDate_stillGeneratesOptions(): void
    {
        // 结束日期在开始日期之前
        $beginDate = Carbon::create(2023, 1, 10);
        $endDate = Carbon::create(2023, 1, 5);

        $this->request->setBeginDate($beginDate);
        $this->request->setEndDate($endDate);

        $options = $this->request->getRequestOptions();

        // 即使日期顺序不正确，请求选项仍然会生成
        $this->assertArrayHasKey('json', $options);
        $this->assertSame('2023-01-10', $options['json']['begin_date']);
        $this->assertSame('2023-01-05', $options['json']['end_date']);
    }
}
