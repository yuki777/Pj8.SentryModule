<?php

namespace Pj8\SentryModule;

use BEAR\Resource\Module\ResourceModule;
use BEAR\Resource\ResourceInterface;
use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Exception\UnsupportedTypeException;
use Ray\Aop\ReflectiveMethodInvocation;
use Ray\Di\Injector;

use function dirname;
use function restore_error_handler;
use function set_error_handler;
use function str_contains;

use const E_DEPRECATED;
use const PHP_VERSION_ID;

class ResourceInterceptorTest extends TestCase
{
    private ?Transaction $transaction;
    private ?ResourceTrace $trace;

    protected function setUp(): void
    {
        parent::setUp();
        if (PHP_VERSION_ID < 80100) {
            return;
        }

        // Ray.Di 2.13.0後方互換のため refs. https://github.com/ray-di/Ray.Di/releases/tag/2.13.1
        set_error_handler([$this, 'ignoreRayDiDeprecatedError']);
    }

    private function ignoreRayDiDeprecatedError(int $errno, string $s, string $file): bool
    {
        return $errno === E_DEPRECATED && str_contains($file, dirname(__DIR__) . '/vendor/ray/di');
    }

    protected function tearDown(): void
    {
        restore_error_handler();
        parent::tearDown();
    }

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

    public function testInvokeCallsTransactionCaseFirstSpan(): void
    {
        $injector = new Injector(new ResourceModule('FakeApplication'), __DIR__ . '/tmp');

        $resource = $injector->getInstance(ResourceInterface::class);
        $fakeAppRo = $resource->get('app://self/index');

        $mockTrace = $this->createMock(ResourceTraceInterface::class);
        $mockTrace->expects($this->once())->method('isFirstSpan')->willReturn(true);
        $mockTrace->expects($this->once())->method('setTransaction')->with($fakeAppRo);
        $interceptor = new ResourceInterceptor($mockTrace);

        $invocation = new ReflectiveMethodInvocation($fakeAppRo, 'onGet', [$interceptor]);
        $interceptor->invoke($invocation);
    }

    public function testInvokeReturnsAppResourceCaseAppResource(): void
    {
        $injector = new Injector(new ResourceModule('FakeApplication'), __DIR__ . '/tmp');

        $resource = $injector->getInstance(ResourceInterface::class);
        $fakeAppRo = $resource->get('app://self/index');

        $interceptor = $this->createResourceInterceptor();

        $invocation = new ReflectiveMethodInvocation($fakeAppRo, 'onGet', [$interceptor]);
        $fakeRoResult = $interceptor->invoke($invocation);

        $this->assertSame($fakeAppRo, $fakeRoResult);
    }

    private function createResourceInterceptor(): ResourceInterceptor
    {
        $dryRun = ['dsn' => null];
        $this->transaction = new Transaction($dryRun, 'test-dummy');
        $this->trace = new ResourceTrace($this->transaction, new Span($this->transaction), new SpanContextFactory(new ResourceSpanFactory()));

        return new ResourceInterceptor($this->trace);
    }
}
