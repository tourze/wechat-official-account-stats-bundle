<?php

declare(strict_types=1);

namespace WechatOfficialAccountStatsBundle\Tests\Request;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\RunTestsInSeparateProcesses;
use Tourze\PHPUnitSymfonyKernelTest\AbstractIntegrationTestCase;
use WechatOfficialAccountStatsBundle\Request\AbstractRequest;

/**
 * @internal
 */
#[RunTestsInSeparateProcesses]
#[CoversClass(AbstractRequest::class)]
final class AbstractRequestTest extends AbstractIntegrationTestCase
{
    protected function onSetUp(): void
    {
        // 这个测试类不需要设置任何内容
    }

    public function testAbstractRequestIsAbstract(): void
    {
        $reflection = new \ReflectionClass(AbstractRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testAbstractRequestExtendsApiRequest(): void
    {
        $reflection = new \ReflectionClass(AbstractRequest::class);
        $parentClass = $reflection->getParentClass();
        $this->assertNotFalse($parentClass);
        $this->assertSame('HttpClientBundle\Request\ApiRequest', $parentClass->getName());
    }
}
