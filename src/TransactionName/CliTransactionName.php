<?php

declare(strict_types=1);

namespace Pj8\SentryModule\TransactionName;

class CliTransactionName implements TransactionNameInterface
{
    private CliTransactionNameBuilder $builder;
    private string $name;

    public function __construct(CliTransactionNameBuilder $builder)
    {
        $this->builder = $builder;
        $this->name = '';
    }

    /**
     * CLIのトランザクション名
     *
     * 出力例： "cli - /foo/bar"
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getName(): string
    {
        // Web実行時にはコマンドライン引数が無いので生成時ではなく実行時に評価
        $this->name = $this->builder->buildBy($GLOBALS);

        return $this->name;
    }
}
