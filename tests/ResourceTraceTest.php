<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Fake\FakeRo;
use Ray\Aop\ReflectiveMethodInvocation;
use Sentry\Tracing\SpanContext;

class ResourceTraceTest extends TestCase
{
    public function testStartWithDispatchSpanStart(): void
    {
        $dryRun = ['dsn' => null];
        $transaction = new Transaction($dryRun, 'dummy');

        $mockSpan = $this->createMock(SpanInterface::class);
        $mockSpan->expects($this->once())->method('start');

        $factory = new SpanContextFactory(new ResourceSpanFactory());
        $resourceTrace = new ResourceTrace($transaction, $mockSpan, $factory);

        $stub = new FakeRo();
        $invocation = new ReflectiveMethodInvocation($stub, 'onGet', []);
        $resourceTrace->startWith($invocation);
        unset($transaction);
    }

    public function testSetCurrentSpanByUpdateSpan(): void
    {
        $transaction = $this->dryRunStart();
        $sentrySpan = $transaction->startChild(new SpanContext());

        $mockSpan = $this->createMock(SpanInterface::class);
        $mockSpan->expects($this->once())->method('getCurrentSpan')->willReturn($sentrySpan);
        $mockSpan->expects($this->once())->method('setCurrentSpan');

        $factory = new SpanContextFactory(new ResourceSpanFactory());

        $resourceTrace = new ResourceTrace($transaction, $mockSpan, $factory);
        $resourceTrace->setCurrentSpanBy(new FakeRo());
        unset($transaction);
    }

    public function testSetTransactionByUpdateTransaction(): void
    {
        $transaction = $this->dryRunStart();
        $sentryTransaction = $transaction->getTransaction();

        $mockTransaction = $this->createMock(TransactionInterface::class);
        $mockTransaction->expects($this->once())->method('getTransaction')->willReturn($sentryTransaction);
        $mockTransaction->expects($this->once())->method('setTransaction');

        $span = new Span(new Transaction(['dsn' => null], 'test-dummy'));

        $factory = new SpanContextFactory(new ResourceSpanFactory());

        $resourceTrace = new ResourceTrace($mockTransaction, $span, $factory);
        $stub = new FakeRo();
        $invocation = new ReflectiveMethodInvocation($stub, 'onGet', []);
        $resourceTrace->startWith($invocation);
        $resourceTrace->setTransactionBy(new FakeRo());
        unset($transaction);
    }

    private function dryRunStart(): Transaction
    {
        $dryRun = ['dsn' => null];

        return new Transaction($dryRun, 'test-dummy');
    }
}
