<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel\ContainerAccessLevels;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Sjpereira\AzureStoragePhpSdk\Concerns\HasManager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

/**
 * @phpstan-import-type PropertiesType from Properties
 *
 * @phpstan-type ContainerType array{Name?: string, Deleted?: bool, Version?: string, Properties?: PropertiesType}
 */
final class Container
{
    /** @use HasManager<ContainerManager> */
    use HasManager;

    public readonly string $name;

    public readonly bool $deleted;

    public readonly string $version;

    public readonly Properties $properties;

    /** @param ContainerType $container */
    public function __construct(array $container)
    {
        /** @var string $name */
        $name = ($container['Name'] ?? '');

        if (empty($name)) {
            throw RequiredFieldException::missingField('Name');
        }

        $this->name       = $name;
        $this->deleted    = to_boolean($container['Deleted'] ?? false);
        $this->version    = $container['Version'] ?? '';
        $this->properties = new Properties($container['Properties'] ?? []);
    }

    public function listAccessLevels(): ContainerAccessLevels
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->accessLevel()->list($this->name);
    }

    public function properties(): ContainerProperties
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->getProperties($this->name);
    }

    public function metadata(): ContainerMetadata
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->metadata()->get($this->name);
    }

    public function delete(): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->delete($this->name);
    }

    public function restore(): bool
    {
        $this->ensureManagerIsConfigured();

        return $this->getManager()->restore($this->name, $this->version);
    }
}
