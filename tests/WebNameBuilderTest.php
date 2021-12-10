<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;

class WebNameBuilderTest extends TestCase
{
    public function testWebNameBuilderReturnsNameCaseNormal(): void
    {
        $builder = new WebNameBuilder();
        $fixture = [
            'HTTP_HOST' => 'foo.dummy.test.com',
            'REQUEST_URI' => 'https://foo.dummy.test.com/aaa?a=b',
        ];
        $name = $builder->buildBy($fixture);
        $this->assertSame($name, 'foo.dummy.test.com - /aaa');
    }

    public function testWebNameBuilderReturnsNameCaseNoHost(): void
    {
        $builder = new WebNameBuilder();
        $name = $builder->buildBy(['REQUEST_URI' => 'https://foo.dummy.test.com/']);
        $this->assertSame($name, 'web - unknown');
    }

    public function testWebNameBuilderReturnsNameCaseNoUri(): void
    {
        $builder = new WebNameBuilder();
        $name = $builder->buildBy(['HTTP_HOST' => 'aaa']);
        $this->assertSame($name, 'web - unknown');
    }
}
