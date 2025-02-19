<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Neos;

use Neos\Flow\Core\Booting\Scripts;
use Neos\Flow\Package\PackageManager;
use Netlogix\DataDeploymentTasks\Task;
use Neos\Flow\Annotations as Flow;

final class IndexElasticsearch implements Task
{
    private array $settings = [];

    #[Flow\InjectConfiguration(package: 'Neos.Flow')]
    protected array $flowSettings = [];

    public function __construct(private PackageManager $packageManager)
    {
    }

    public function injectSettings(array $settings): void
    {
        $this->settings = $settings['elasticsearch'] ?? [];
    }

    public function isEnabled(string $destination): bool
    {
        if (!$this->packageManager->isPackageAvailable('Flowpack.ElasticSearch.ContentRepositoryAdaptor')) {
            return false;
        }

        return $this->settings['enabled'] ?? false;
    }

    public function run(string $destination): void
    {
        Scripts::executeCommand($this->determineCommand(), $this->flowSettings, true, [
            'workspace' => 'live'
        ]);
    }

    private function determineCommand(): string
    {
        if ($this->packageManager->isPackageAvailable('Flowpack.ElasticSearch.ContentRepositoryQueueIndexer')) {
            return 'flowpack.elasticsearch.contentrepositoryqueueindexer:nodeindexqueue:build';
        }

        return 'flowpack.elasticsearch.contentrepositoryadaptor:nodeindex:build';
    }

    public static function order(): int
    {
        return 1000;
    }
}
