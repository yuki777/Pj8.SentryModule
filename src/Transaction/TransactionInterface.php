<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Transaction;

interface TransactionInterface
{
    public function startTransaction(): void;

    public function finishTransaction(): void;
}
