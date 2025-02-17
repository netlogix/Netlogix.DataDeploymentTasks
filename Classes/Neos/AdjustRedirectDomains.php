<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Neos;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager as DoctrineEntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Neos\RedirectHandler\DatabaseStorage\RedirectStorage;
use Netlogix\DataDeploymentTasks\Task;
use RuntimeException;

final class AdjustRedirectDomains implements Task
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
        $this->settings = $settings['redirects'] ?? [];
    }

    public function isEnabled(string $destination): bool
    {
        return $this->settings['enabled'] ?? false;
    }

    public function run(string $destination): void
    {
        if (!class_exists(RedirectStorage::class)) {
            throw new RuntimeException(sprintf(
                'Package neos/redirecthandler-databasestorage is not available. Task "%s" must be disabled!',
                AdjustRedirectDomains::class
            ), 1669727900);
        }

        if (!array_key_exists($destination, $this->settings['domains'] ?? [])) {
            throw new RuntimeException(sprintf(
                'Redirect domains for destination "%s" are not configured',
                $destination
            ), 1669727499);
        }

        $domains = $this->settings['domains'][$destination];

        foreach ($domains as $from => $to) {
            $this
                ->dbal
                ->executeStatement(
                    <<<SQL
UPDATE neos_redirecthandler_databasestorage_domain_model_redirect SET
    host = REPLACE(host, :from, :to),
    targeturipath = REPLACE(targeturipath, :from, :to)
SQL
                    ,
                    [
                        'from' => $from,
                        'to' => $to,
                    ]
                );
        }

        $this
            ->dbal
            ->executeStatement(
                <<<SQL
UPDATE neos_redirecthandler_databasestorage_domain_model_redirect SET
    targeturipathhash = MD5(targeturipath)
SQL
            );
    }
}
