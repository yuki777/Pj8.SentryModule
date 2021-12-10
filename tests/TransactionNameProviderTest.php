<?php

namespace Pj8\SentryModule;

use PHPUnit\Framework\TestCase;

class TransactionNameProviderTest extends TestCase
{
    public function testGetReturnsStringCaseCli(): void
    {
        $cli = new CliNameBuilder();
        $web = new WebNameBuilder();
        $provider = new TransactionNameProvider($cli, $web);
        $result = $provider->get();

        $this->assertNotEmpty($result);
    }
}
