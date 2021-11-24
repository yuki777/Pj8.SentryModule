<?php

declare(strict_types=1);

namespace Pj8\SentryModule\TransactionName;

use PHPUnit\Framework\TestCase;

class CliTransactionNameBuilderTest extends TestCase
{
    public function testCliTransactionNameBuilderReturnsNameCaseNormal(): void
    {
        $builder = new CliTransactionNameBuilder();
        $fixture = [
            'argv' => ['aaa.sh'],
        ];
        $name = $builder->buildBy($fixture);
        $this->assertSame($name, 'cli - aaa.sh');
    }

    public function testCliTransactionNameBuilderReturnsPathCaseNormal(): void
    {
        $builder = new CliTransactionNameBuilder();
        $fixture = [
            'argv' => ['aaa.sh', 'get', '/abc/123'],
        ];
        $name = $builder->buildBy($fixture);
        $this->assertSame($name, 'cli - aaa.sh - /abc/123');
    }

    public function testCliTransactionNameBuilderReturnsNameCaseNoArgv(): void
    {
        $builder = new CliTransactionNameBuilder();
        $name = $builder->buildBy([]);
        $this->assertSame($name, 'cli - unknown');
    }

    public function testCliTransactionNameBuilderReturnsNameCaseEmptyArgv(): void
    {
        $builder = new CliTransactionNameBuilder();
        $name = $builder->buildBy(['argv' => '']);
        $this->assertSame($name, 'cli - unknown');
    }
}
