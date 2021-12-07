<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Ray\Aop\MethodInvocation;
use Sentry\Tracing\SpanContext;

use function json_encode;
use function sprintf;

final class SpanContextFactory implements SpanContextFactoryInterface
{
    private ResourceSpanFactory $factory;

    public function __construct(ResourceSpanFactory $title)
    {
        $this->factory = $title;
    }

    public function __invoke(MethodInvocation $invocation): SpanContext
    {
        $object = $invocation->getThis();
        if ($object instanceof ResourceObject) {
            return ($this->factory)($invocation);
        }

        return $this->getGenericTitle($invocation);
    }

    private function getGenericTitle(MethodInvocation $invocation): SpanContext
    {
        $spanContext = new SpanContext();
        $spanContext->setOp(sprintf(
            '%s::%s (%s)',
            $invocation->getMethod()->getDeclaringClass()->getName(),
            $invocation->getMethod()->getName(),
            (string) json_encode((array) $invocation->getNamedArguments())
        ));

        return $spanContext;
    }
}
