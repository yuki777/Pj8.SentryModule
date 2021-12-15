<?php

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Ray\Aop\MethodInvocation;

interface ResourceTraceInterface
{
    public function start(MethodInvocation $invocation): void;

    public function setCurrentSpan(ResourceObject $result): void;

    public function setTransaction(ResourceObject $result): void;

    public function isFirstSpan(): bool;

    public function finish(): void;
}
