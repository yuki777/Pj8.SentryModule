<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\SamplingContext;

interface TracesSamplerInterface
{
    public function __invoke(SamplingContext $context): float;
}
