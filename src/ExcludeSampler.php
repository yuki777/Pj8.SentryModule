<?php

declare(strict_types=1);

namespace Pj8\SentryModule;

use Sentry\Tracing\SamplingContext;

use function array_map;
use function strpos;
use function strtolower;

final class ExcludeSampler implements TracesSamplerInterface
{
    private float $defaultRate;

    /** @var string[] */
    private array $excludeNames;

    /**
     * @param float         $default      トレース計測レートのデフォルト値
     * @param array<string> $excludeNames トレース計測から常に除外したいトランザクション名の配列（オプション）
     */
    public function __construct(float $default, array $excludeNames = [])
    {
        $this->defaultRate = $default;
        $this->excludeNames = $excludeNames;
    }

    public function __invoke(SamplingContext $context): float
    {
        $transactionContext = $context->getTransactionContext();
        if (! $transactionContext) {
            return 0.0;
        }

        if ($this->isExclude($transactionContext->getName())) {
            return 0.0;
        }

        return $this->defaultRate;
    }

    private function isExclude(string $transactionName): bool
    {
        if (empty($this->excludeNames)) {
            return false;
        }

        foreach (array_map('strtolower', $this->excludeNames) as $excludeName) {
            if (strpos(strtolower($transactionName), $excludeName) !== false) {
                return true;
            }
        }

        return false;
    }
}
