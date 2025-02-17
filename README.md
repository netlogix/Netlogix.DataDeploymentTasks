# Netlogix.DataDeploymentTasks

This package provides tasks that can be run after deploying a data snapshot to a Flow or Neos instance.
The goal of these tasks is to adjust the target environment to the new data that was deployed by e.g. adjusting
domain records.

## Installation

```shell
composer require netlogix/datadeploymenttasks
```

## Usage

To execute the configured tasks after a data deployment, run
```shell
./flow datadeployment:runtasks <target environment name>
```

## Configuration

See [Settings.Redirects.yaml](Configuration/Settings.Redirects.yaml)
