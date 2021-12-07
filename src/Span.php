<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Pj8\SentryModule\Transaction\TransactionInterface;
use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanContext;

use function array_pop;
use function end;

final class Span implements SpanInterface
{
    /** @var array<TracingSpan> */
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

    /**
     * @param array<string, string|null> $config
     */
    public function start(SpanContext $context): void
    {
        $span = $this->getSpan();
        $this->spans[] = $span->startChild($context);
    }

    public function finish(): void
    {
        $span = array_pop($this->spans);
        $span->finish();
    }

    /**
     * @return TracingSpan|StartChildInterface
     */
    private function getSpan()
    {
        if ($this->spans) {
            return end($this->spans);
        }

        $this->spans[] = $this->transaction;

        return $this->transaction;
    }
}
