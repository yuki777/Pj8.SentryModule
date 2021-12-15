<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;
use Pj8\SentryModule\Exception\InvalidArgumentException;

class SentryErrorModuleTest extends TestCase
{
    public function testConstructorThrowsExceptionCaseNull(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new SentryErrorModule();
    }
}
