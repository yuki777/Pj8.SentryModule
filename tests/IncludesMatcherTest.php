<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Fake\FakeRo;
use ReflectionMethod;

class IncludesMatcherTest extends TestCase
{
    /**
     * @dataProvider getHttpMethodData
     */
    public function testMatchesMethodMatchesHttpMethod(ReflectionMethod $method): void
    {
        $matcher = new IsHttpMethodMatcher();
        $this->assertTrue($matcher->matchesMethod($method, []), 'method: ' . $method->getName());
    }

    /**
     * @dataProvider getUnrelatedMethodData
     */
    public function testMatchesMethodUnmatchesUnrelatedMethod(ReflectionMethod $method): void
    {
        $matcher = new IsHttpMethodMatcher();
        $this->assertFalse($matcher->matchesMethod($method, []), 'method: ' . $method->getName());
    }

    /**
     * @return ReflectionMethod[][]
     */
    public function getHttpMethodData(): array
    {
        return [
            [new ReflectionMethod(FakeRo::class, 'onGet')],
            [new ReflectionMethod(FakeRo::class, 'onPost')],
            [new ReflectionMethod(FakeRo::class, 'onPut')],
            [new ReflectionMethod(FakeRo::class, 'onDelete')],
        ];
    }

    /**
     * @return ReflectionMethod[][]
     */
    public function getUnrelatedMethodData(): array
    {
        return [
            [new ReflectionMethod(FakeRo::class, 'bar')],
        ];
    }
}
