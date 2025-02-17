<?php

declare(strict_types=1);

namespace Netlogix\DataDeploymentTasks;

interface Task
{
    public function isEnabled(string $destination): bool;

    public function run(string $destination): void;
}
