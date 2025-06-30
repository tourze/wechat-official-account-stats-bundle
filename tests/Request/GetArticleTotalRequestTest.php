<?php

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Request\GetArticleTotalRequest;

class GetArticleTotalRequestTest extends TestCase
{
    private GetArticleTotalRequest $request;

    protected function setUp(): void
    {
        $this->request = new GetArticleTotalRequest();
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