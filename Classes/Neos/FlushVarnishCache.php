<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Neos;

use Neos\Flow\Cache\CacheManager;
use Neos\Flow\Core\Booting\Scripts;
use Neos\Flow\Package\PackageManager;
use Netlogix\DataDeploymentTasks\Task;
use Neos\Flow\Annotations as Flow;

final class FlushVarnishCache implements Task
{
    #[Flow\InjectConfiguration(package: 'Neos.Flow')]
    protected array $flowSettings = [];

    public function __construct(private PackageManager $packageManager)
    {
    }

    public function isEnabled(string $destination): bool
    {
        return $this->packageManager->isPackageAvailable('Flowpack.Varnish');
    }

    public function run(string $destination): void
    {
        Scripts::executeCommand('flowpack.varnish:varnish:clear', $this->flowSettings);
    }

    public static function order(): int
    {
        return 200;
    }
}
