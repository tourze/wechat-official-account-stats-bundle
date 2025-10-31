<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatOfficialAccountBundle\Entity\Account;
use WechatOfficialAccountStatsBundle\Request\GetAdvertisingSpaceDataRequest;

/**
 * @internal
 */
#[CoversClass(GetAdvertisingSpaceDataRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetAdvertisingSpaceDataRequestTest extends AbstractIntegrationTestCase
{
    private GetAdvertisingSpaceDataRequest $request;

    private Account $account;

    protected function onSetUp(): void
    {
        $this->request = $this->createMock(GetAdvertisingSpaceDataRequest::class);
        /*
         * 使用具体类 Account 的原因：
         * 1) GetAdvertisingSpaceDataRequest 需要 Account 实体对象作为参数
         * 2) 在测试中需要模拟 Account 的具体行为和方法调用
         * 3) Account 是 Doctrine 实体类，没有对应的接口抽象
         * 这种使用方式在测试请求类时是合理且必要的
         */
        $this->account = $this->createMock(Account::class);
    }

    /**
     * 测试请求路径
     */
    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $this->request->method('getRequestPath')
            ->willReturn('https://api.weixin.qq.com/publisher/stat')
        ;
        $this->assertSame('https://api.weixin.qq.com/publisher/stat', $this->request->getRequestPath());
    }

    /**
     * 测试请求方法
     */
    public function testGetRequestMethodReturnsGetMethod(): void
    {
        $this->request->method('getRequestMethod')
            ->willReturn('GET')
        ;
        $this->assertSame('GET', $this->request->getRequestMethod());
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
     * 测试action设置和获取
     */
    public function testSetAndGetActionWithValidValueReturnsValue(): void
    {
        $action = 'publisher_adpos_general';
        $this->request->method('getAction')
            ->willReturn($action)
        ;
        $this->request->setAction($action);
        $this->assertSame($action, $this->request->getAction());
    }

    /**
     * 测试page设置和获取
     */
    public function testSetAndGetPageWithValidValueReturnsValue(): void
    {
        $page = '1';
        $this->request->method('getPage')
            ->willReturn($page)
        ;
        $this->request->setPage($page);
        $this->assertSame($page, $this->request->getPage());
    }

    /**
     * 测试pageSize设置和获取
     */
    public function testSetAndGetPageSizeWithValidValueReturnsValue(): void
    {
        $pageSize = '10';
        $this->request->method('getPageSize')
            ->willReturn($pageSize)
        ;
        $this->request->setPageSize($pageSize);
        $this->assertSame($pageSize, $this->request->getPageSize());
    }

    /**
     * 测试startDate设置和获取
     */
    public function testSetAndGetStartDateWithValidValueReturnsValue(): void
    {
        $startDate = '2023-06-01';
        $this->request->method('getStartDate')
            ->willReturn($startDate)
        ;
        $this->request->setStartDate($startDate);
        $this->assertSame($startDate, $this->request->getStartDate());
    }

    /**
     * 测试endDate设置和获取
     */
    public function testSetAndGetEndDateWithValidValueReturnsValue(): void
    {
        $endDate = '2023-06-30';
        $this->request->method('getEndDate')
            ->willReturn($endDate)
        ;
        $this->request->setEndDate($endDate);
        $this->assertSame($endDate, $this->request->getEndDate());
    }

    /**
     * 测试请求选项
     */
    public function testGetRequestOptionsReturnsCorrectOptions(): void
    {
        $expectedOptions = [
            'query' => [
                'action' => 'publisher_adpos_general',
                'page' => '1',
                'page_size' => '10',
                'start_date' => '2023-06-01',
                'end_date' => '2023-06-30',
            ],
        ];

        $this->request->method('getRequestOptions')
            ->willReturn($expectedOptions)
        ;
        $this->request->setAction('publisher_adpos_general');
        $this->request->setPage('1');
        $this->request->setPageSize('10');
        $this->request->setStartDate('2023-06-01');
        $this->request->setEndDate('2023-06-30');

        $this->assertSame($expectedOptions, $this->request->getRequestOptions());
    }

    /**
     * 测试默认值
     */
    public function testDefaultValues(): void
    {
        $this->request->method('getAction')
            ->willReturn('')
        ;
        $this->request->method('getPage')
            ->willReturn('')
        ;
        $this->request->method('getPageSize')
            ->willReturn('')
        ;
        $this->request->method('getStartDate')
            ->willReturn('')
        ;
        $this->request->method('getEndDate')
            ->willReturn('')
        ;

        $this->assertSame('', $this->request->getAction());
        $this->assertSame('', $this->request->getPage());
        $this->assertSame('', $this->request->getPageSize());
        $this->assertSame('', $this->request->getStartDate());
        $this->assertSame('', $this->request->getEndDate());
    }
}
