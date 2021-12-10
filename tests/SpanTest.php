<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Sentry\Tracing\Span as SentrySpan;
use Sentry\Tracing\SpanContext;

class SpanTest extends TestCase
{
    public function testStartCreateElementCaseEmpty(): void
    {
        $span = $this->createSpan();
        $fixture = new SpanContext();
        $span->start($fixture);

        $result = $span->getCurrentSpan();
        $this->assertNotNull($result);
        $this->assertInstanceOf(SentrySpan::class, $result);
    }

    public function testGetCurrentSpanReturnsNullCaseEmpty(): void
    {
        $span = $this->createSpan();
        $result = $span->getCurrentSpan();
        $this->assertNull($result);
    }

    public function testIsFirstReturnsTrueCaseFirstTransaction(): void
    {
        $span = $this->createSpan();
        $fixture = new SpanContext();
        $span->start($fixture);

        $result = $span->isFirst();
        $this->assertTrue($result);
    }

    public function testIsFirstReturnsFalseCase2ndSpan(): void
    {
        $span = $this->createSpan();
        $fixture = new SpanContext();
        $span->start($fixture);
        $span->start($fixture);

        $result = $span->isFirst();
        $this->assertFalse($result);
    }

    private function createSpan(): Span
    {
        $dryRun = ['dsn' => null];
        $transaction = new Transaction($dryRun, 'dummy');

        return new Span($transaction);
    }
}
