<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Flow;

use Neos\Flow\Core\Booting\Scripts;
use Neos\Flow\Package\PackageManager;
use Netlogix\DataDeploymentTasks\Task;
use Neos\Flow\Annotations as Flow;

final class DoctrineMigrations implements Task
{
    #[Flow\InjectConfiguration(package: 'Neos.Flow')]
    protected array $flowSettings = [];

    public function __construct()
    {
    }

    public function isEnabled(string $destination): bool
    {
        return true;
    }

    public function run(string $destination): void
    {
        Scripts::executeCommand('neos.flow:doctrine:migrate', $this->flowSettings);
    }

    public static function order(): int
    {
        return 5;
    }
}
