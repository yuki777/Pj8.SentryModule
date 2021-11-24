<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Exception\UnsupportedInvocationTypeException;
use Pj8\SentryModule\Fake\FakeRoForInterceptor;
use Ray\Aop\ReflectiveMethodInvocation;

class PerformanceInterceptorTest extends TestCase
{
    public function testInvokeThrowsExceptionCaseNotResourceObject(): void
    {
        $this->expectException(UnsupportedInvocationTypeException::class);

        $dummySpan = new SpanClient();
        $interceptor = new PerformanceInterceptor($dummySpan);

        $fixture = static function (): string {
            return 'callable but not "ResourceObject"';
        };
        $invocation = new ReflectiveMethodInvocation($fixture, '__invoke', [$interceptor]);
        $interceptor->invoke($invocation);
    }

    public function testInvokeNotStartSentrySpanCaseNoTransaction(): void
    {
        // トランザクションがない場合
        $mockSpan = $this->createMock(SpanClient::class);
        $mockSpan->expects($this->once())->method('getCurrentHubSpan')
            ->willReturn(null);
        // Span を開始しない
        $mockSpan->expects($this->never())->method('startChild');

        $interceptor = new PerformanceInterceptor($mockSpan);

        $fakeRo = new FakeRoForInterceptor();
        $invocation = new ReflectiveMethodInvocation($fakeRo, 'onGet', [$interceptor]);
        $interceptor->invoke($invocation);
    }
}
