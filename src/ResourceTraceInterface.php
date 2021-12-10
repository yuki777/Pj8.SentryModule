<?php

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Ray\Aop\MethodInvocation;

interface ResourceTraceInterface
{
    public function startWith(MethodInvocation $invocation): void;

    public function setCurrentSpanBy(ResourceObject $result): void;

    public function setTransactionBy(ResourceObject $result): void;

    public function isFirstSpan(): bool;

    public function finish(): void;
}
