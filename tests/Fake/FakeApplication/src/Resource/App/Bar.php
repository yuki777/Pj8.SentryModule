<?php

declare(strict_types=1);

namespace FakeApplication\Resource\App;

use BEAR\Resource\ResourceObject;

class Bar extends ResourceObject
{
    public function onGet(): ResourceObject
    {
        $this->body = 'result by app bar resource';

        return $this;
    }
}
