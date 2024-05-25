<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

readonly class Container
{
    public string $name;

    public Properties $properties;

    public function __construct(array $container)
    {
        $this->name       = $container['Name'] ?? ''; // TODO: throw exception if name does not exist
        $this->properties = new Properties($container['Properties'] ?? []);
    }
}
