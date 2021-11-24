<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\SentrySdk;
use Sentry\Tracing\Span;
use Sentry\Tracing\SpanContext;

class SpanClient
{
    private ?Span $span = null;

    public function getCurrentHubSpan(): ?Span
    {
        return SentrySdk::getCurrentHub()->getSpan();
    }

    /**
     * @param array<string, string|null> $childConfig
     */
    public function startChild(Span $parent, array $childConfig = ['op' => null]): ?Span
    {
        $spanContext = new SpanContext();
        if (! $childConfig['op']) {
            // 想定外だが例外にはしない
            return null;
        }

        $spanContext->setOp($childConfig['op']);
        $this->span = $parent->startChild($spanContext);

        return $this->span;
    }

    public function finish(): void
    {
        // 該当 Span 無しは想定外だが例外にはしない
        if (! $this->span) {
            return;
        }

        $this->span->finish();
    }
}
