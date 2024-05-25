<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

readonly class Container
{
    public string $name;

    public Properties $property;

    public function __construct(array $container)
    {
        $this->name     = $container['Name'] ?? ''; // TODO: throw exception if name does not exist
        $this->property = new Properties($container['Properties'] ?? []);
    }
}
