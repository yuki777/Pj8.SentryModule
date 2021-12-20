<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\Transaction as SentryTransaction;

interface TransactionInterface extends StartChildInterface
{
    public function getTransaction(): SentryTransaction;
}
