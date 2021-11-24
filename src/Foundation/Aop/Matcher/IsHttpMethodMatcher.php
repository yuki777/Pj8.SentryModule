<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Foundation\Aop\Matcher;

class IsHttpMethodMatcher extends EqualsToMatcher
{
    public function __construct()
    {
        parent::__construct([
            'onGet',
            'onPost',
            'onPut',
            'onPatch',
            'onDelete',
        ]);
    }
}
