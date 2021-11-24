<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Sentry\Tracing\TransactionContext;

use function Sentry\startTransaction;

class SpanClientTest extends TestCase
{
    public function testStartChildSkipInvalidInput(): void
    {
        $transactionContext = new TransactionContext();
        $transactionContext->setName('dummy');
        $transaction = startTransaction($transactionContext);

        $spanClient = new SpanClient();
        $child = $spanClient->startChild($transaction, ['op' => null]);
        $this->assertNull($child);
    }
}
