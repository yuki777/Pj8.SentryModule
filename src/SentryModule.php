<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use InvalidArgumentException;
use Pj8\SentryModule\Annotation\Monitorable;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

use function array_key_exists;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SentryModule extends AbstractModule
{
    /** @var array<string, ?string> Sentry SDK 初期化オプション */
    private array $config;

    /**
     * @param array<string, ?string> $config Sentry SDK 初期化オプション
     *
     * @see https://docs.sentry.io/platforms/php/configuration/options/
     */
    public function __construct(array $config)
    {
        $this->guardInvalid($config);
        $this->config = $config;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->bind()->annotatedWith('sentry-options')->toInstance($this->config);
        $this->bind(CliNameBuilder::class);
        $this->bind(WebNameBuilder::class);
        $this->bind()->annotatedWith('sentry-tr-name')->toProvider(TransactionNameProvider::class);
        $this->bind(TransactionInterface::class)->to(Transaction::class)->in(Scope::SINGLETON);
        $this->bind(SpanInterface::class)->to(Span::class);
        $this->bind(SpanContextFactoryInterface::class)->to(SpanContextFactory::class);
        $this->bind(ResourceSpanFactory::class);

        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Monitorable::class),
            [MonitorInterceptor::class]
        );
    }

    /**
     * @param array<string, ?string> $config
     */
    private function guardInvalid(array $config): void
    {
        if (! array_key_exists('dsn', $config)) {
            throw new InvalidArgumentException();
        }
    }
}
