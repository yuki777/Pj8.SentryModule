<?php

declare(strict_types=1);

namespace Pj8\SentryModule\TransactionName;

use PHPUnit\Framework\TestCase;

class WebTransactionNameBuilderTest extends TestCase
{
    public function testWebTransactionNameBuilderReturnsNameCaseNormal(): void
    {
        $builder = new WebTransactionNameBuilder();
        $fixture = [
            'HTTP_HOST' => 'foo.dummy.test.com',
            'REQUEST_URI' => 'https://foo.dummy.test.com/aaa?a=b',
        ];
        $name = $builder->buildBy($fixture);
        $this->assertSame($name, 'foo.dummy.test.com - /aaa');
    }

    public function testWebTransactionNameBuilderReturnsNameCaseNoHost(): void
    {
        $builder = new WebTransactionNameBuilder();
        $name = $builder->buildBy(['REQUEST_URI' => 'https://foo.dummy.test.com/']);
        $this->assertSame($name, 'web - unknown');
    }

    public function testCliTransactionNameBuilderReturnsNameCaseNoUri(): void
    {
        $builder = new WebTransactionNameBuilder();
        $name = $builder->buildBy(['HTTP_HOST' => 'aaa']);
        $this->assertSame($name, 'web - unknown');
    }
}
