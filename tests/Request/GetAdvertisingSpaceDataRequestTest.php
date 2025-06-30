<?php

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;

class GetAdvertisingSpaceDataRequestTest extends TestCase
{
    private GetAdvertisingSpaceDataRequest $request;
    private Account $account;

    protected function setUp(): void
    {
        $this->request = new GetAdvertisingSpaceDataRequest();
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试请求路径
     */
    public function testGetRequestPath_returnsCorrectPath(): void
    {
        $this->assertSame('https://api.weixin.qq.com/publisher/stat', $this->request->getRequestPath());
    }

    /**
     * 测试请求方法
     */
    public function testGetRequestMethod_returnsGetMethod(): void
    {
        $this->assertSame('GET', $this->request->getRequestMethod());
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
     * 测试action设置和获取
     */
    public function testSetAndGetAction_withValidValue_returnsValue(): void
    {
        $action = 'publisher_adpos_general';
        $this->request->setAction($action);
        $this->assertSame($action, $this->request->getAction());
    }

    /**
     * 测试page设置和获取
     */
    public function testSetAndGetPage_withValidValue_returnsValue(): void
    {
        $page = '1';
        $this->request->setPage($page);
        $this->assertSame($page, $this->request->getPage());
    }

    /**
     * 测试pageSize设置和获取
     */
    public function testSetAndGetPageSize_withValidValue_returnsValue(): void
    {
        $pageSize = '10';
        $this->request->setPageSize($pageSize);
        $this->assertSame($pageSize, $this->request->getPageSize());
    }

    /**
     * 测试startDate设置和获取
     */
    public function testSetAndGetStartDate_withValidValue_returnsValue(): void
    {
        $startDate = '2023-06-01';
        $this->request->setStartDate($startDate);
        $this->assertSame($startDate, $this->request->getStartDate());
    }

    /**
     * 测试endDate设置和获取
     */
    public function testSetAndGetEndDate_withValidValue_returnsValue(): void
    {
        $endDate = '2023-06-30';
        $this->request->setEndDate($endDate);
        $this->assertSame($endDate, $this->request->getEndDate());
    }

    /**
     * 测试请求选项
     */
    public function testGetRequestOptions_returnsCorrectOptions(): void
    {
        $this->request->setAction('publisher_adpos_general');
        $this->request->setPage('1');
        $this->request->setPageSize('10');
        $this->request->setStartDate('2023-06-01');
        $this->request->setEndDate('2023-06-30');

        $expectedOptions = [
            'query' => [
                'action' => 'publisher_adpos_general',
                'page' => '1',
                'page_size' => '10',
                'start_date' => '2023-06-01',
                'end_date' => '2023-06-30',
            ],
        ];

        $this->assertSame($expectedOptions, $this->request->getRequestOptions());
    }

    /**
     * 测试默认值
     */
    public function testDefaultValues(): void
    {
        $this->assertSame('', $this->request->getAction());
        $this->assertSame('', $this->request->getPage());
        $this->assertSame('', $this->request->getPageSize());
        $this->assertSame('', $this->request->getStartDate());
        $this->assertSame('', $this->request->getEndDate());
    }
}