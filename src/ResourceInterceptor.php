<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Pj8\SentryModule\Exception\UnsupportedTypeException;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;
use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanStatus;

/**
 * @SuppressWarnings(PHPMD.StaticAccess)
 */
final class ResourceInterceptor implements MethodInterceptor, MonitorInterceptorInterface
{
    private TransactionInterface $transaction;
    private SpanInterface $span;
    private SpanContextFactoryInterface $factory;
    private static bool $initialized = false;

    public function __construct(
        TransactionInterface $transaction,
        SpanInterface $span,
        SpanContextFactoryInterface $factory
    ) {
        $this->transaction = $transaction;
        $this->span = $span;
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        if (! ($invocation->getThis() instanceof ResourceObject)) {
            throw new UnsupportedTypeException();
        }

        $spanContext = ($this->factory)($invocation);
        $this->span->start($spanContext);
        $result = $invocation->proceed();
        if (! ($result instanceof ResourceObject)) {
            return $result;
        }

        $tracingSpan = $this->span->getCurrentSpan();
        if ($tracingSpan instanceof TracingSpan) {
            /** @psalm-suppress StaticAccess */
            $tracingSpan->setStatus(SpanStatus::createFromHttpStatusCode($result->code));
            $this->span->setCurrentSpan($tracingSpan);
        }

        // Embed のような遅延評価リソースではないエンドポイントリソースの場合
        if (self::$initialized === false && $this->span->isFirst()) {
            self::$initialized = true;
            $tracingTran = $this->transaction->getTransaction();
            /** @psalm-suppress StaticAccess */
            $tracingTran->setStatus(SpanStatus::createFromHttpStatusCode($result->code));
            $this->transaction->setTransaction($tracingTran);
        }

        $this->span->finish();

        return $result;
    }
}
