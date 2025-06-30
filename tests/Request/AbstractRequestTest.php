<?php

namespace WechatOfficialAccountStatsBundle\Tests\Unit\Request;

use PHPUnit\Framework\TestCase;
use WechatOfficialAccountStatsBundle\Request\AbstractRequest;

class AbstractRequestTest extends TestCase
{
    public function testAbstractRequestIsAbstract(): void
    {
        $reflection = new \ReflectionClass(AbstractRequest::class);
        $this->assertTrue($reflection->isAbstract());
    }

    public function testAbstractRequestExtendsApiRequest(): void
    {
        $reflection = new \ReflectionClass(AbstractRequest::class);
        $this->assertSame('HttpClientBundle\Request\ApiRequest', $reflection->getParentClass()->getName());
    }
}