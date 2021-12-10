<?php

declare(strict_types=1);

namespace FakeApplication\Resource\App;

use BEAR\Resource\ResourceObject;

class Aaa extends ResourceObject
{
    const RESULT = 'result by app Bar resource';

    public function onGet(): ResourceObject
    {
        $this->body = self::RESULT;

        return $this;
    }
}
