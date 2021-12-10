<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;

class CliNameBuilderTest extends TestCase
{
    public function testCliTransactionNameBuilderReturnsNameCaseNormal(): void
    {
        $builder = new CliNameBuilder();
        $fixture = [
            'argv' => ['aaa.sh'],
        ];
        $name = $builder->buildBy($fixture);
        $this->assertSame($name, 'cli - aaa.sh');
    }

    public function testCliTransactionNameBuilderReturnsPathCaseNormal(): void
    {
        $builder = new CliNameBuilder();
        $fixture = [
            'argv' => ['aaa.sh', 'get', '/abc/123'],
        ];
        $name = $builder->buildBy($fixture);
        $this->assertSame($name, 'cli - aaa.sh - get - /abc/123');
    }

    public function testCliTransactionNameBuilderReturnsNameCaseNoArgv(): void
    {
        $builder = new CliNameBuilder();
        $name = $builder->buildBy([]);
        $this->assertSame($name, 'cli - unknown');
    }

    public function testCliTransactionNameBuilderReturnsNameCaseEmptyArgv(): void
    {
        $builder = new CliNameBuilder();
        $name = $builder->buildBy(['argv' => '']);
        $this->assertSame($name, 'cli - unknown');
    }
}
