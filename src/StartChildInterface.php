<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanContext;

interface StartChildInterface
{
    public function startChild(SpanContext $context): TracingSpan;
}
