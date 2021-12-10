<?php

declare(strict_types=1);

namespace FakeApplication\Resource\Page;

use BEAR\Resource\ResourceObject;
use BEAR\Sunday\Inject\ResourceInject;

class Foo extends ResourceObject
{
    use ResourceInject;

    public function onGet(): ResourceObject
    {
        $this->body = $this->resource->get('app://self/bar')->body;

        return $this;
    }
}
