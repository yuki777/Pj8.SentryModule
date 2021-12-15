<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Ray\Aop\MethodInvocation;
use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanStatus;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class ResourceTrace implements ResourceTraceInterface
{
    private TransactionInterface $transaction;
    private SpanInterface $span;
    private SpanContextFactoryInterface $factory;

    public function __construct(
        TransactionInterface $transaction,
        SpanInterface $span,
        SpanContextFactoryInterface $factory
    ) {
        $this->transaction = $transaction;
        $this->span = $span;
        $this->factory = $factory;
    }

    public function start(MethodInvocation $invocation): void
    {
        $spanContext = ($this->factory)($invocation);
        $this->span->start($spanContext);
    }

    public function setCurrentSpan(ResourceObject $result): void
    {
        $tracingSpan = $this->span->getCurrentSpan();
        if (! ($tracingSpan instanceof TracingSpan)) {
            return;
        }

        /** @psalm-suppress StaticAccess */
        $tracingSpan->setStatus(SpanStatus::createFromHttpStatusCode($result->code));
        $this->span->setCurrentSpan($tracingSpan);
    }

    public function setTransaction(ResourceObject $result): void
    {
        if (! $this->span->isFirst()) {
            return;
        }

        $tracingTran = $this->transaction->getTransaction();
        /** @psalm-suppress StaticAccess */
        $tracingTran->setStatus(SpanStatus::createFromHttpStatusCode($result->code));
        $this->transaction->setTransaction($tracingTran);
    }

    public function isFirstSpan(): bool
    {
        return $this->span->isFirst();
    }

    public function finish(): void
    {
        $this->span->finish();
    }

    public function __destruct()
    {
        unset($this->span);
        unset($this->transaction);
    }
}
