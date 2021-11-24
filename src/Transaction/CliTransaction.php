<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Transaction;

use Pj8\SentryModule\TransactionName\TransactionNameInterface;
use Ray\Di\Di\Named;
use Sentry\SentrySdk;
use Sentry\Tracing\Transaction;
use Sentry\Tracing\TransactionContext;

use function Sentry\init;
use function Sentry\startTransaction;

class CliTransaction implements TransactionInterface
{
    /** @var array<string, string> */
    private array $options;
    private TransactionNameInterface $cliTransactionName;
    private static string $operation = 'backend';

    /**
     * @param array<string, string> $options
     *
     * @Named("options=sentry-options,cli=sentry-tr-cli-name")
     */
    public function __construct(array $options, TransactionNameInterface $cli)
    {
        $this->options = $options;
        $this->cliTransactionName = $cli;
    }

    public function startTransaction(): void
    {
        init($this->options);

        $transactionContext = new TransactionContext();
        $transactionContext->setName($this->cliTransactionName->getName());
        $transactionContext->setOp(self::$operation);
        $transaction = startTransaction($transactionContext);

        $this->setCurrentHubSpan($transaction);
    }

    public function finishTransaction(): void
    {
        $transaction = SentrySdk::getCurrentHub()->getSpan();
        if (! $transaction) {
            return;
        }

        $transaction->finish();
    }

    private function setCurrentHubSpan(Transaction $transaction): void
    {
        // ハブに設定しないと後からトランザクションが取り出せない
        // See https://docs.sentry.io/platforms/php/guides/laravel/performance/
        SentrySdk::getCurrentHub()->setSpan($transaction);
    }
}
