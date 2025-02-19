<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks\Command;

use Neos\Flow\Cli\CommandController;
use Neos\Flow\ObjectManagement\ObjectManagerInterface;
use Neos\Flow\Reflection\ReflectionService;
use Netlogix\DataDeploymentTasks\Task;
use RuntimeException;
use Neos\Flow\Annotations as Flow;

final class DataDeploymentCommandController extends CommandController
{
    protected array $taskClassNames = [];

    public function __construct()
    {
        parent::__construct();
    }

    public function initializeObject(): void
    {
        $this->taskClassNames = self::collectTasks($this->objectManager);
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

            $this->outputLine('<success>Running Task</success> "%s"', [$taskClassName]);
            $task->run($destination);
            $this->outputLine();
        }
    }

    #[Flow\CompileStatic]
    protected static function collectTasks(ObjectManagerInterface $objectManager): array
    {
        $reflectionService = $objectManager->get(ReflectionService::class);
        $taskClassNames = $reflectionService->getAllImplementationClassNamesForInterface(Task::class);
        usort($taskClassNames, static fn (string $a, string $b) => $a::order() <=> $b::order());

        return $taskClassNames;
    }
}
