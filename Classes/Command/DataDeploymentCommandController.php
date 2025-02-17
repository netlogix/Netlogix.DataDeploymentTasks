<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Command;

use Neos\Flow\Cli\CommandController;
use Netlogix\DataDeploymentTasks\Neos;
use Netlogix\DataDeploymentTasks\Task;
use RuntimeException;

final class DataDeploymentCommandController extends CommandController
{
    protected array $taskClassNames = [Neos\AdjustRedirectDomains::class, Neos\AdjustSiteDomains::class];

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Run tasks after a data deployment
     *
     * @param string $destination Name of the destination of the dump
     */
    public function runTasksCommand(string $destination): void
    {
        foreach ($this->taskClassNames as $taskClassName) {
            if (!is_a($taskClassName, Task::class, true)) {
                throw new RuntimeException(
                    sprintf('Class "%s" is not of type %s', $taskClassName, Task::class),
                    1669727081
                );
            }
        }

        foreach ($this->taskClassNames as $taskClassName) {
            $task = $this->objectManager->get($taskClassName);
            assert($task instanceof Task);

            if (!$task->isEnabled($destination)) {
                $this->outputLine('Skipping Task "%s" as it is disabled.', [$taskClassName]);

                continue;
            }

            $this->outputLine('Running Task "%s"', [$taskClassName]);
            $task->run($destination);
        }
    }
}
