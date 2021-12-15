<?php

declare(strict_types=1);

namespace FakeApplication\Resource\App;

use BEAR\Resource\ResourceObject;

class Index extends ResourceObject
{
    const RESULT = 'result by app resource';

    public function onGet(): ResourceObject
    {
        $this->body = self::RESULT;

        return $this;
    }
}
