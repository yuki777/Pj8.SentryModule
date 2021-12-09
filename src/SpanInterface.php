<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\SpanContext;

interface SpanInterface
{
    /**
     * @param array<string, string|null> $config
     */
    public function start(SpanContext $context): void;

    public function finish(): void;
}
