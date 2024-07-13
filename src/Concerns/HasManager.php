<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Concerns;

use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\ManagerNotSetException;

/** @template TManager of Manager */
trait HasManager
{
    /** @var TManager */
    protected Manager $manager;

    /** @param TManager $manager */
    public function setManager(Manager $manager): static
    {
        $this->manager = $manager;

        return $this;
    }

    /** @return TManager */
    public function getManager(): Manager
    {
        return $this->manager;
    }

    protected function ensureManagerIsConfigured(): void
    {
        if (!isset($this->manager)) {
            throw ManagerNotSetException::create();
        }
    }
}
