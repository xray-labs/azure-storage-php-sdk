<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

final readonly class Container
{
    public string $name;

    public Properties $properties;

    public function __construct(protected ContainerManager $manager, array $container)
    {
        if (($name = ($container['Name'] ?? '')) === '') {
            throw RequiredFieldException::missingField('Name');
        }

        $this->name       = $name;
        $this->properties = new Properties($container['Properties'] ?? []);
    }

    public function delete(): bool
    {
        return $this->manager->delete($this->name);
    }

    public function listBlobs(): array
    {
        return [];
    }
}
