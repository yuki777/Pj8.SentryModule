<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Sentry\Tracing\SamplingContext;
use Sentry\Tracing\TransactionContext;

class ExcludeSamplerTest extends TestCase
{
    /**
     * @dataProvider getSampleExcludeData
     */
    public function testInvokeSamplingEqualsHealthCheck(string $excludeConf, string $transactionName): void
    {
        $default = 0.5;
        $sampler = new ExcludeSampler($default, [$excludeConf]);

        $transactionContext = new TransactionContext($transactionName);
        $samplingContext = SamplingContext::getDefault($transactionContext);
        $result = $sampler->__invoke($samplingContext);

        $this->assertNotSame($default, $result);
        $this->assertSame(0.0, $result);
    }

    /**
     * @dataProvider getSampleExcludeData
     */
    public function testInvokeReturnsFloatZeroCaseEnvKeyNotFound(): void
    {
        $sampler = new ExcludeSampler((float) false, []);

        $transactionContext = new TransactionContext('dummy');
        $samplingContext = SamplingContext::getDefault($transactionContext);
        $result = $sampler->__invoke($samplingContext);

        $this->assertSame(0.0, $result);
    }

    /**
     * @return mixed[][]
     */
    public function getSampleExcludeData(): array
    {
        return [
            [
                '/healthcheck',
                'https://example.com - /healthcheck',
            ],
            [
                'healthcheck',
                '/healthcheck',
            ],
            [
                'healthcheck',
                '/healthCheck',
            ],
        ];
    }
}
