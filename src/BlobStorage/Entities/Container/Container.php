<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use Exception;

readonly class Container
{
    public string $name;

    public Properties $properties;

    public function __construct(array $container)
    {
        if (($name = ($container['Name'] ?? '')) === '') {
            throw new Exception('Name is required'); // TODO: Create Custom Exception
        }

        $this->name       = $name;
        $this->properties = new Properties($container['Properties'] ?? []);
    }

    public function listBlobs()
    {
        return [];
    }
}
