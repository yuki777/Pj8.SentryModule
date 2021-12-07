<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\ResourceObject;
use Ray\Aop\MethodInvocation;
use Sentry\Tracing\SpanContext;

use function assert;
use function sprintf;

final class ResourceSpanFactory implements SpanContextFactoryInterface
{
    public function __invoke(MethodInvocation $invocation): SpanContext
    {
        $object = $invocation->getThis();
        assert($object instanceof ResourceObject);

        $spanContaxt = new SpanContext();
        $spanContaxt->setOp(sprintf('%s %s', $object->uri->method, (string) $object->uri));

        return $spanContaxt;
    }
}
