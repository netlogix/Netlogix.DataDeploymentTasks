<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Netlogix;

use Neos\Flow\Core\Booting\Scripts;
use Neos\Flow\Package\PackageManager;
use Netlogix\DataDeploymentTasks\Task;
use Neos\Flow\Annotations as Flow;

final class NetlogixMigrations implements Task
{
    #[Flow\InjectConfiguration(package: 'Neos.Flow')]
    protected array $flowSettings = [];

    public function __construct(private PackageManager $packageManager)
    {
    }

    public function isEnabled(string $destination): bool
    {
        return $this->packageManager->isPackageAvailable('Netlogix.Migrations');
    }

    public function run(string $destination): void
    {
        Scripts::executeCommand('netlogix.migrations:migrations:migrate', $this->flowSettings);
    }

    public static function order(): int
    {
        return 10;
    }
}
