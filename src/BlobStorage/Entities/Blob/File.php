<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;

/**
 * @phpstan-type FileType array{Name?: string, Content-Length?: string, Content-Type?: string, Content-MD5?: string, Last-Modified?: string, Accept-Ranges?: string, ETag?: string, Vary?: string, Server?: string, x-ms-request-id?: string, x-ms-version?: string, x-ms-creation-time?: string, x-ms-lease-status?: string, x-ms-lease-state?: string, x-ms-blob-type?: string, x-ms-server-encrypted?: bool, Date?: string}
 */
final class File
{
    public readonly string $content;

    public readonly string $name;

    public readonly string $contentLength;

    public readonly string $contentType;

    public readonly string $contentMD5;

    public readonly string $lastModified;

    public readonly string $acceptRanges;

    public readonly string $etag;

    public readonly string $vary;

    public readonly string $server;

    public readonly string $xMsRequestId;

    public readonly DateTimeImmutable $xMsVersion;

    public readonly DateTimeImmutable $xMsCreationTime;

    public readonly string $xMsLeaseStatus;

    public readonly string $xMsLeaseState;

    public readonly string $xMsBlobType;

    public readonly bool $xMsServerEncrypted;

    public readonly DateTimeImmutable $date;

    /** @param FileType $file */
    public function __construct(string $content, array $file)
    {
        $this->content            = $content;
        $this->name               = $file['Name'] ?? '';
        $this->contentLength      = $file['Content-Length'] ?? '';
        $this->contentType        = $file['Content-Type'] ?? '';
        $this->contentMD5         = $file['Content-MD5'] ?? '';
        $this->lastModified       = $file['Last-Modified'] ?? '';
        $this->acceptRanges       = $file['Accept-Ranges'] ?? '';
        $this->etag               = $file['ETag'] ?? '';
        $this->vary               = $file['Vary'] ?? '';
        $this->server             = $file['Server'] ?? '';
        $this->xMsRequestId       = $file['x-ms-request-id'] ?? '';
        $this->xMsVersion         = new DateTimeImmutable($file['x-ms-version'] ?? 'now');
        $this->xMsCreationTime    = new DateTimeImmutable($file['x-ms-creation-time'] ?? 'now');
        $this->xMsLeaseStatus     = $file['x-ms-lease-status'] ?? '';
        $this->xMsLeaseState      = $file['x-ms-lease-state'] ?? '';
        $this->xMsBlobType        = $file['x-ms-blob-type'] ?? '';
        $this->xMsServerEncrypted = to_boolean($file['x-ms-server-encrypted'] ?? true);
        $this->date               = new DateTimeImmutable($file['Date'] ?? 'now');
    }

    public function stream(): void
    {
        header("Content-Disposition: inline; filename=\"{$this->name}\"");

        $this->handleFileToDownloadOrStream();
    }

    public function download(): void
    {
        header("Content-Disposition: attachment; filename=\"{$this->name}\"");

        $this->handleFileToDownloadOrStream();
    }

    protected function handleFileToDownloadOrStream(): void
    {
        header("Content-Type: {$this->contentType}");
        header("Content-Length: {$this->contentLength}");
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: 0');

        echo $this->content;
    }
}
