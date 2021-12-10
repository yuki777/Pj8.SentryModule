<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

interface MonitorInterceptorInterface extends MethodInterceptor
{
    /**
     * @return mixed
     *
     * @see "Distributed Tracing" https://docs.sentry.io/product/sentry-basics/tracing/distributed-tracing/
     */
    public function invoke(MethodInvocation $invocation);
}
