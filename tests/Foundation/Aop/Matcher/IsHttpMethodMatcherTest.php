<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Foundation\Aop\Matcher;

use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Foundation\Aop\Matcher\Fake\FakeRoForMatcher;
use ReflectionMethod;

class IsHttpMethodMatcherTest extends TestCase
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
     * @return mixed[][]
     */
    public function getHttpMethodData(): array
    {
        return [
            [new ReflectionMethod(FakeRoForMatcher::class, 'onGet')],
            [new ReflectionMethod(FakeRoForMatcher::class, 'onPost')],
        ];
    }

    /**
     * @return mixed[][]
     */
    public function getUnrelatedMethodData(): array
    {
        return [
            [new ReflectionMethod(FakeRoForMatcher::class, 'bar')],
        ];
    }
}
