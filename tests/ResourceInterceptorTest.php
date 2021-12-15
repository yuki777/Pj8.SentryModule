<?php

namespace Pj8\SentryModule;

use BEAR\Resource\Module\ResourceModule;
use BEAR\Resource\ResourceInterface;
use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Exception\UnsupportedTypeException;
use Ray\Aop\ReflectiveMethodInvocation;
use Ray\Di\Injector;

class ResourceInterceptorTest extends TestCase
{
    private ?Transaction $transaction;
    private ?ResourceTrace $trace;

    public function testInvokeThrowsExceptionCaseNotResource(): void
    {
        $this->expectException(UnsupportedTypeException::class);

        $interceptor = $this->createResourceInterceptor();

        $fixture = static function (): string {
            return 'callable but not ResourceObject';
        };
        $invocation = new ReflectiveMethodInvocation($fixture, '__invoke', [$interceptor]);
        $interceptor->invoke($invocation);
    }

    /**
     * @runInSeparateProcess
     */
    public function testInvokeReturnsAppResourceCaseAppResource(): void
    {
        $injector = new Injector(new ResourceModule('FakeApplication'), __DIR__ . '/tmp');

        $resource = $injector->getInstance(ResourceInterface::class);
        $fakeAppRo = $resource->get('app://self/aaa');

        $interceptor = $this->createResourceInterceptor();

        $invocation = new ReflectiveMethodInvocation($fakeAppRo, 'onGet', [$interceptor]);
        $fakeRoResult = $interceptor->invoke($invocation);

        $this->assertSame($fakeAppRo, $fakeRoResult);
    }

    /**
     * @runInSeparateProcess
     */
    public function testInvokeCallsTransactionCaseFirstSpan(): void
    {
        $injector = new Injector(new ResourceModule('FakeApplication'), __DIR__ . '/tmp');

        $resource = $injector->getInstance(ResourceInterface::class);
        $fakeAppRo = $resource->get('app://self/aaa');

        $mockTrace = $this->createMock(ResourceTraceInterface::class);
        $mockTrace->expects($this->once())->method('isFirstSpan')->willReturn(true);
        $mockTrace->expects($this->once())->method('setTransaction')->with($fakeAppRo);
        $interceptor = new ResourceInterceptor($mockTrace);

        $invocation = new ReflectiveMethodInvocation($fakeAppRo, 'onGet', [$interceptor]);
        $interceptor->invoke($invocation);
    }

    private function createResourceInterceptor(): ResourceInterceptor
    {
        $dryRun = ['dsn' => null];
        $this->transaction = new Transaction($dryRun, 'test-dummy');
        $this->trace = new ResourceTrace($this->transaction, new Span($this->transaction), new SpanContextFactory(new ResourceSpanFactory()));

        return new ResourceInterceptor($this->trace);
    }
}
