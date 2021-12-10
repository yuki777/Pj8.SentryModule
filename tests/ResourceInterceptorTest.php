<?php

namespace Pj8\SentryModule;

use BEAR\Resource\Module\ResourceModule;
use BEAR\Resource\ResourceInterface;
use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Exception\UnsupportedTypeException;
use Ray\Aop\ReflectiveMethodInvocation;
use Ray\Di\Injector;

use function array_map;
use function glob;

use const GLOB_BRACE;

class ResourceInterceptorTest extends TestCase
{
    private ?Transaction $transaction;
    private ?ResourceTrace $trace;

    public function testInvokeThrowsExceptionCaseNotResource(): void
    {
        $this->expectException(UnsupportedTypeException::class);

        $interceptor = $this->createResourceTrace();

        $fixture = static function (): string {
            return 'callable but not ResourceObject';
        };
        $invocation = new ReflectiveMethodInvocation($fixture, '__invoke', [$interceptor]);
        $interceptor->invoke($invocation);

        $this->unsetTrace();
    }

    public function testInvokeReturnAppResourceCaseAppResource(): void
    {
        $this->cleanupFakeTmpDir();

        $injector = new Injector(new ResourceModule('FakeApplication'), __DIR__ . '/tmp');

        $resource = $injector->getInstance(ResourceInterface::class);
        $fakeAppRo = $resource->get('app://self/aaa');

        $interceptor = $this->createResourceTrace();

        $invocation = new ReflectiveMethodInvocation($fakeAppRo, 'onGet', [$interceptor]);
        $fakeRoResult = $interceptor->invoke($invocation);

        $this->assertSame($fakeAppRo, $fakeRoResult);

        $this->unsetTrace();
        $this->cleanupFakeTmpDir();
    }

    private function cleanupFakeTmpDir(): void
    {
        $dirs = glob(__DIR__ . '/tmp/{*.txt,*.php}', GLOB_BRACE);
        if (! $dirs) {
            return;
        }

        array_map('unlink', $dirs);
    }

    private function createResourceTrace(): ResourceInterceptor
    {
        $dryRun = ['dsn' => null];
        $this->transaction = new Transaction($dryRun, 'test-dummy');
        $this->trace = new ResourceTrace($this->transaction, new Span($this->transaction), new SpanContextFactory(new ResourceSpanFactory()));

        return new ResourceInterceptor($this->trace);
    }

    private function unsetTrace(): void
    {
        $this->transaction = null;
        $this->trace = null;
    }
}
