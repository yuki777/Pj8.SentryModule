<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\Span as TracingSpan;
use Sentry\Tracing\SpanContext;

interface SpanInterface
{
    public function start(SpanContext $context): void;

    public function finish(): void;

    /**
     * @return ?TracingSpan
     */
    public function getCurrentSpan();

    public function isFirst(): bool;
}
