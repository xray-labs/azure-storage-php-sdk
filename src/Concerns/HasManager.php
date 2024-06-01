<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Concerns;

use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\ManagerNotSetException;

trait HasManager
{
    protected Manager $manager;

    public function setManager(Manager $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    public function getManager(): Manager
    {
        return $this->manager;
    }

    protected function ensureManagerIsConfigured(): never
    {
        if (!isset($this->manager)) {
            throw ManagerNotSetException::create();
        }
    }
}
