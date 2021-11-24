<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Pj8\SentryModule\Exception\UnsupportedInvocationTypeException;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

use function sprintf;

class PerformanceInterceptor implements MethodInterceptor
{
    private SpanClient $spanClient;

    public function __construct(SpanClient $span)
    {
        $this->spanClient = $span;
    }

    /**
     * @return mixed
     *
     * @see "Distributed Tracing" https://docs.sentry.io/product/sentry-basics/tracing/distributed-tracing/
     */
    public function invoke(MethodInvocation $invocation)
    {
        // トランザクションはブートストラップで管理する。
        // Embed のAppリソースは遅延評価されることから、
        // トランザクション終了処理をいつ呼べば良いかインターセプターで判断することはできない。
        $ro = $invocation->getThis();
        if (! ($ro instanceof ResourceObject)) {
            throw new UnsupportedInvocationTypeException();
        }

        $transaction = $this->spanClient->getCurrentHubSpan();
        if ($transaction === null) {
            return $invocation->proceed();
        }

        $operation = sprintf('%s - %s', $ro->uri->scheme, $ro->uri->path);
        $this->spanClient->startChild($transaction, ['op' => $operation]);

        $result = $invocation->proceed();

        $this->spanClient->finish();

        return $result;
    }
}
