<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Neos;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Netlogix\DataDeploymentTasks\Task;
use RuntimeException;

final class AdjustSiteDomains implements Task
{
    private array $settings = [];

    private Connection $dbal;

    public function __construct(EntityManagerInterface $entityManager)
    {
        assert($entityManager instanceof DoctrineEntityManager);
        $this->dbal = $entityManager->getConnection();
    }

    public function injectSettings(array $settings): void
    {
        $this->settings = $settings['sites'] ?? [];
    }

    public function isEnabled(string $destination): bool
    {
        return $this->settings['enabled'] ?? false;
    }

    public function run(string $destination): void
    {
        if (!array_key_exists($destination, $this->settings['domains'] ?? [])) {
            throw new RuntimeException(sprintf(
                'Site domains for destination "%s" are not configured',
                $destination
            ), 1739806928);
        }

        $domains = $this->settings['domains'][$destination];

        foreach ($domains as $from => $to) {
            $this
                ->dbal
                ->executeStatement(
                    <<<SQL
UPDATE neos_neos_domain_model_domain SET
    hostname = REPLACE(hostname, :from, :to)
SQL
                    ,
                    [
                        'from' => $from,
                        'to' => $to,
                    ]
                );
        }
    }
}
