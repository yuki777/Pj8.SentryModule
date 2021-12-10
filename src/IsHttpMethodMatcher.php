<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

class IsHttpMethodMatcher extends IncludesMatcher
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
