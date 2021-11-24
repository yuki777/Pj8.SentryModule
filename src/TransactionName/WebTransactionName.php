<?php

declare(strict_types=1);

namespace Pj8\SentryModule\TransactionName;

class WebTransactionName implements TransactionNameInterface
{
    private WebTransactionNameBuilder $builder;
    private string $name;

    public function __construct(WebTransactionNameBuilder $builder)
    {
        $this->builder = $builder;
        $this->name = '';
    }

    /**
     * Web のトランザクション名
     *
     * 例："aaa.bbb.jp - /foo/bar"
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public function getName(): string
    {
        // CLI実行時にはHTTPサーバーの環境変数は無いため生成時ではなく実行時に評価
        $this->name = $this->builder->buildBy($_SERVER);

        return $this->name;
    }
}
