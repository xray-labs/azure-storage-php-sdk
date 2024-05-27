<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

class Containers
{
    protected array $containers = [];

    public function __construct(array $containers = [])
    {
        foreach ($containers as $container) {
            $this->containers[] = new Container($container);
        }
    }

    public function all(): array
    {
        return $this->containers;
    }

    public function first(): ?Container
    {
        return $this->containers[0] ?? null;
    }

    public function last(): ?Container
    {
        return $this->containers[count($this->containers) - 1] ?? null;
    }

    public function count(): int
    {
        return count($this->containers);
    }

    public function isEmpty(): bool
    {
        return empty($this->containers);
    }

    public function isNotEmpty(): bool
    {
        return !empty($this->containers);
    }
}
