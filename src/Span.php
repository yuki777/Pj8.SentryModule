<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanContext;
use Sentry\Tracing\Transaction;

use function array_pop;
use function count;
use function end;

final class Span implements SpanInterface
{
    /** @var array<(TracingSpan|mixed)> */
    private array $spans = [];
    private TransactionInterface $transaction;

    public function __construct(TransactionInterface $transaction)
    {
        $this->transaction = $transaction;
    }

    public function __destruct()
    {
        unset($this->transaction);
    }

    public function start(SpanContext $context): void
    {
        $span = $this->getCurrentSpan();
        if ($span === null) {
            $span = $this->transaction->getTransaction();
        }

        $this->spans[] = $span->startChild($context);
    }

    public function finish(): void
    {
        if (count($this->spans) === 0) {
            return;
        }

        $span = array_pop($this->spans);
        if (! $span) {
            return;
        }

        $span->finish();
    }

    /**
     * @return TracingSpan|Transaction|null
     */
    public function getCurrentSpan()
    {
        if ($this->spans) {
            return end($this->spans);
        }

        return null;
    }

    public function setCurrentSpan(?TracingSpan $span): void
    {
        if (count($this->spans) === 0) {
            return;
        }

        $span = array_pop($this->spans);
        if (! $span) {
            return;
        }

        $this->spans[] = $span;
    }

    public function isFirst(): bool
    {
        // First Span (Transaction's child)
        return count($this->spans) === 1;
    }
}
