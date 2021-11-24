<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Sentry\Tracing\SamplingContext;
use Sentry\Tracing\TransactionContext;

class TracesSamplerTest extends TestCase
{
    /**
     * @dataProvider getSampleExcludeData
     */
    public function testSampleSamplingEqualsHealthCheck(string $exclude, string $path): void
    {
        $default = 0.5;
        $sampler = new TracesSampler($default, [$exclude]);

        $transactionContext = new TransactionContext($path);
        $samplingContext = SamplingContext::getDefault($transactionContext);
        $result = $sampler->sample($samplingContext);

        $this->assertNotSame($default, $result);
        $this->assertSame(0.0, $result);
    }

    /**
     * @dataProvider getSampleExcludeData
     */
    public function testSampleReturnsFloatZeroCaseEnvKeyNotFound(): void
    {
        $sampler = new TracesSampler((float) false, []);

        $transactionContext = new TransactionContext('dummy');
        $samplingContext = SamplingContext::getDefault($transactionContext);
        $result = $sampler->sample($samplingContext);

        $this->assertSame(0.0, $result);
    }

    /**
     * @return mixed[][]
     */
    public function getSampleExcludeData(): array
    {
        return [
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
