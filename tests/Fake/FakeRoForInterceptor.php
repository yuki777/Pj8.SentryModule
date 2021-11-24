<?php

declare(strict_types=1);

namespace Pj8\SentryModule\Fake;

use BEAR\Resource\ResourceObject;
use BEAR\Resource\Uri;

class FakeRoForInterceptor extends ResourceObject
{
    public function __construct()
    {
        $this->uri = new Uri('dummy://foo/bar', []);
    }

    public function onGet(): ResourceObject
    {
        return $this;
    }
}
