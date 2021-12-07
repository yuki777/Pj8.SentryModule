<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use BEAR\Resource\Annotation\Monitorable;
use BEAR\Resource\ResourceObject;
use InvalidArgumentException;
use Pj8\SentryModule\Foundation\Aop\Matcher\IsHttpMethodMatcher;
use Pj8\SentryModule\Transaction\CliTransaction;
use Pj8\SentryModule\Transaction\Transaction;
use Pj8\SentryModule\Transaction\TransactionInterface;
use Pj8\SentryModule\Transaction\WebTransaction;
use Pj8\SentryModule\TransactionName\CliTransactionName;
use Pj8\SentryModule\TransactionName\CliTransactionNameBuilder;
use Pj8\SentryModule\TransactionName\TransactionNameInterface;
use Pj8\SentryModule\TransactionName\WebTransactionName;
use Pj8\SentryModule\TransactionName\WebTransactionNameBuilder;
use Ray\Di\AbstractModule;
use Ray\Di\Scope;

use function array_key_exists;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class SentryModule extends AbstractModule
{
    /** @var array<string, string> Sentry SDK 初期化オプション */
    private array $config;

    /**
     * @param array<string, string> $config Sentry SDK 初期化オプション
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
        $this->bind(WebTransactionName::class);
        $this->bind(CliTransactionName::class);
        $this->bind(CliTransactionNameBuilder::class);
        $this->bind(WebTransactionNameBuilder::class);
        $this->bind(TransactionNameInterface::class)->annotatedWith('sentry-tr-web-name')->to(WebTransactionName::class);
        $this->bind(TransactionNameInterface::class)->annotatedWith('sentry-tr-cli-name')->to(CliTransactionName::class);
        $this->bind(TransactionInterface::class)->annotatedWith('sentry-transaction-web')->to(WebTransaction::class);
        $this->bind(TransactionInterface::class)->annotatedWith('sentry-transaction-cli')->to(CliTransaction::class);
        $this->bind(TransactionInterface::class)->to(Transaction::class)->in(Scope::SINGLETON);
        $this->bind(SpanClient::class);
        $this->bind(SpanContextFactoryInterface::class)->to(SpanContextFactory::class);
        $this->bind(SpanInterface::class)->to(Span::class);
        $this->bindInterceptor(
            $this->matcher->subclassesOf(ResourceObject::class),
            new IsHttpMethodMatcher(),
            [MonitorInterceptorInteterface::class]
        );
        $this->bindInterceptor(
            $this->matcher->any(),
            $this->matcher->annotatedWith(Monitorable::class),
            [MonitorInterceptorInteterface::class]
        );
    }

    /**
     * @param array<string, string> $config
     */
    private function guardInvalid(array $config): void
    {
        if (! array_key_exists('dsn', $config)) {
            throw new InvalidArgumentException();
        }

        if (! isset($config['environment'])) {
            throw new InvalidArgumentException();
        }

        if (! isset($config['sample_rate'])) {
            throw new InvalidArgumentException();
        }

        if (! isset($config['traces_sampler']) && ! $config['traces_sample_rate']) {
            throw new InvalidArgumentException();
        }
    }
}
