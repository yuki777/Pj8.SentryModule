<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Ray\Aop\AbstractMatcher;
use ReflectionClass;
use ReflectionMethod;

use function array_map;
use function in_array;
use function strtolower;

class IncludesMatcher extends AbstractMatcher
{
    /** @var string[] 同値判定対象 */
    private array $values;

    /**
     * @param string|string[] $values 同値判定対象
     */
    public function __construct($values)
    {
        parent::__construct();

        $this->values = (array) $values;
    }

    /**
     * {@inheritdoc}
     */
    public function matchesClass(ReflectionClass $class, array $arguments): bool
    {
        return in_array(strtolower($class->getName()), array_map('strtolower', $this->values));
    }

    /**
     * {@inheritdoc}
     */
    public function matchesMethod(ReflectionMethod $method, array $arguments): bool
    {
        return in_array(strtolower($method->getShortName()), array_map('strtolower', $this->values));
    }
}
