<?php

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Request\InterfaceSummaryHourDataRequest;

class InterfaceSummaryHourDataRequestTest extends TestCase
{
    private InterfaceSummaryHourDataRequest $request;

    protected function setUp(): void
    {
        $this->request = new InterfaceSummaryHourDataRequest();
    }

    /**
     * 测试请求路径
     */
    public function testGetRequestPath_returnsCorrectPath(): void
    {
        $this->assertNotEmpty($this->request->getRequestPath());
    }

    /**
     * 测试请求方法
     */
    public function testGetRequestMethod_returnsValidMethod(): void
    {
        $method = $this->request->getRequestMethod();
        $this->assertContains($method, ['GET', 'POST', null]);
    }
}