<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Fake\FakeRo;
use Ray\Aop\ReflectiveMethodInvocation;

class MonitorInterceptorTest extends TestCase
{
    public function testInvokeReturnsResult(): void
    {
        $dryRun = ['dsn' => null];
        $transaction = new Transaction($dryRun, 'dummy');
        $span = new Span($transaction);
        $spanContext = new SpanContextFactory(new ResourceSpanFactory());
        $interceptor = new MonitorInterceptor($span, $spanContext);

        $stub = new FakeRo();
        $invocation = new ReflectiveMethodInvocation($stub, 'onGet', [$interceptor]);
        $result = $interceptor->invoke($invocation);

        $this->assertSame($result, $stub);
    }
}
