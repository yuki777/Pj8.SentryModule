<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Pj8\SentryModule\Exception\UnsupportedTypeException;
use Ray\Aop\MethodInterceptor;
use Ray\Aop\MethodInvocation;

final class ResourceInterceptor implements MethodInterceptor, MonitorInterceptorInterface
{
    private static bool $initialized = false;
    private ResourceTraceInterface $trace;

    public function __construct(ResourceTraceInterface $trace)
    {
        $this->trace = $trace;
    }

    /**
     * {@inheritDoc}
     */
    public function invoke(MethodInvocation $invocation)
    {
        if (! ($invocation->getThis() instanceof ResourceObject)) {
            throw new UnsupportedTypeException();
        }

        $this->trace->start($invocation);

        $result = $invocation->proceed();
        if (! ($result instanceof ResourceObject)) {
            return $result;
        }

        $this->trace->setCurrentSpan($result);
        // Embed のような遅延評価リソースではないエンドポイントリソースの場合
        if (self::$initialized === false && $this->trace->isFirstSpan()) {
            self::$initialized = true;
            $this->trace->setTransaction($result);
        }

        $this->trace->finish();

        return $result;
    }
}
