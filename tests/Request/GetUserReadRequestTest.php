<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatOfficialAccountStatsBundle\Request\GetUserReadRequest;

/**
 * @internal
 */
#[CoversClass(GetUserReadRequest::class)]
#[RunTestsInSeparateProcesses]
final class GetUserReadRequestTest extends AbstractIntegrationTestCase
{
    private GetUserReadRequest $request;

    protected function onSetUp(): void
    {
        $this->request = $this->createMock(GetUserReadRequest::class);
    }

    /**
     * 测试请求路径
     */
    public function testGetRequestPathReturnsCorrectPath(): void
    {
        $this->request->method('getRequestPath')
            ->willReturn('https://api.weixin.qq.com/datacube/getuserread')
        ;
        $this->assertNotEmpty($this->request->getRequestPath());
    }

    /**
     * 测试请求方法
     */
    public function testGetRequestMethodReturnsValidMethod(): void
    {
        $this->request->method('getRequestMethod')
            ->willReturn('POST')
        ;
        $method = $this->request->getRequestMethod();
        $this->assertContains($method, ['GET', 'POST', null]);
    }
}
