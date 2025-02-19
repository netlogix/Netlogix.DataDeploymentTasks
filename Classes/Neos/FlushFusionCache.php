<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Neos;

use Neos\Flow\Cache\CacheManager;
use Netlogix\DataDeploymentTasks\Task;

final class FlushFusionCache implements Task
{
    private const string CACHE_IDENTIFIER = 'Neos_Fusion_Content';

    public function __construct(private CacheManager $cacheManager)
    {
    }

    public function isEnabled(string $destination): bool
    {
        return $this->cacheManager->hasCache(self::CACHE_IDENTIFIER);
    }

    public function run(string $destination): void
    {
        $cache = $this->cacheManager->getCache(self::CACHE_IDENTIFIER);
        $cache->flush();
    }

    public static function order(): int
    {
        return 200;
    }
}
