<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

final readonly class Container
{
    public string $name;

    public bool $deleted;

    public string $version;

    public Properties $properties;

    /**
     * Undocumented function
     *
     * @param array<mixed> $container
     */
    public function __construct(protected ContainerManager $manager, array $container)
    {
        /** @var string $name */
        $name = ($container['Name'] ?? '');

        if ($name === '') {
            throw RequiredFieldException::missingField('Name');
        }

        $this->name       = $name;
        $this->deleted    = to_boolean($container['Deleted'] ?? false);
        $this->version    = $container['Version'] ?? '';
        $this->properties = new Properties($container['Properties'] ?? []);
    }

    public function levelAccess(): ContainerLevelAccess
    {
        return $this->manager->levelAccess($this->name);
    }

    public function properties(): ContainerProperty
    {
        return $this->manager->properties($this->name);
    }

    public function metadata(): ContainerMetadata
    {
        return $this->manager->metadata($this->name);
    }

    public function delete(): bool
    {
        return $this->manager->delete($this->name);
    }

    public function restore(): bool
    {
        return $this->manager->restore($this->name, $this->version);
    }

    /**
     * Undocumented function
     *
     * @return array<mixed>
     */
    public function listBlobs(): array
    {
        return [];
    }
}
