<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use DateTimeImmutable;

final readonly class ContainerMetadata
{
    public DateTimeImmutable $lastModified;

    public string $eTag;

    public string $server;

    public string $xMsRequestId;

    public string $xMsVersion;

    public DateTimeImmutable $date;

    /**
     * Undocumented function
     *
     * @param array<string> $containerMetadata
     */
    public function __construct(array $containerMetadata)
    {
        $this->lastModified = new DateTimeImmutable($containerMetadata['Last-Modified'] ?? 'now');
        $this->eTag         = $containerMetadata['ETag'] ?? '';
        $this->server       = $containerMetadata['Server'] ?? '';
        $this->xMsRequestId = $containerMetadata['x-ms-request-id'] ?? '';
        $this->xMsVersion   = $containerMetadata['x-ms-version'] ?? '';
        $this->date         = new DateTimeImmutable($containerMetadata['Date'] ?? 'now');
    }
}
