<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Ray\Aop\MethodInvocation;
use Sentry\Tracing\SpanContext;

interface SpanContextFactoryInterface
{
    public function __invoke(MethodInvocation $invocation): SpanContext;
}
