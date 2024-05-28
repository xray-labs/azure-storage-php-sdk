<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;

final readonly class ContainerLevelAccess
{
    public string $id;

    public ?DateTimeImmutable $accessPolicyStart;

    public ?DateTimeImmutable $accessPolicyExpiry;

    public string $accessPolicyPermission;

    public function __construct(array $containerLevelAccess)
    {
        $signedIdentifier = $containerLevelAccess['SignedIdentifier'] ?? [];

        $this->id                     = $signedIdentifier['Id'] ?? '';
        $this->accessPolicyStart      = isset($signedIdentifier['AccessPolicy']['Start']) ? new DateTimeImmutable($signedIdentifier['AccessPolicy']['Start']) : null;
        $this->accessPolicyExpiry     = isset($signedIdentifier['AccessPolicy']['Expiry']) ? new DateTimeImmutable($signedIdentifier['AccessPolicy']['Expiry']) : null;
        $this->accessPolicyPermission = $signedIdentifier['AccessPolicy']['Permission'] ?? '';
    }
}
