<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Transaction;

use Pj8\SentryModule\TransactionName\TransactionNameInterface;
use Ray\Di\Di\Named;
use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanContext;
use Sentry\Tracing\Transaction as SentryTransaction;
use Sentry\Tracing\TransactionContext;

use function Sentry\init;
use function Sentry\startTransaction;

final class Transaction implements TransactionInterface
{
    /** @var array<string, string> */
    private array $options;
    private TransactionNameInterface $name;
    private SentryTransaction $transaction;
    public const OPERATION = 'backend';

    /**
     * @param array<string,string> $options
     *
     * @Named("options=sentry-options")
     */
    #[Named('options=sentry-options')]
    public function __construct(array $options, TransactionNameInterface $name)
    {
        $this->options = $options;
        $this->name = $name;
        $this->startTransaction();
    }

    public function __destruct()
    {
        $this->finishTransaction();
    }

    private function startTransaction(): void
    {
        init($this->options);

        $transactionContext = new TransactionContext();
        $transactionContext->setName($this->name->getName());
        $transactionContext->setOp(self::OPERATION);
        $this->transaction = startTransaction($transactionContext);
    }

    private function finishTransaction(): void
    {
        $this->transaction->finish();
    }

    public function startChild(SpanContext $context): TracingSpan
    {
        return $this->transaction->startChild($context);
    }
}
