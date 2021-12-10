<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Sentry\Tracing\Transaction as SentryTransaction;

class TransactionTest extends TestCase
{
    public function testGetTransactionReturnsTransactionCaseInitialized(): void
    {
        $dryRun = ['dsn' => null];
        $transaction = new Transaction($dryRun, 'dummy');
        $result = $transaction->getTransaction();
        $this->assertInstanceOf(SentryTransaction::class, $result);
    }
}
