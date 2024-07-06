<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidFileMimeTypeException;

/**
 * @phpstan-type FileType array{Content-Length?: string, Content-Type?: string, Content-MD5?: string, Last-Modified?: string, Accept-Ranges?: string, ETag?: string, Vary?: string, Server?: string, x-ms-request-id?: string, x-ms-version?: string, x-ms-creation-time?: string, x-ms-lease-status?: string, x-ms-lease-state?: string, x-ms-blob-type?: string, x-ms-server-encrypted?: bool, Date?: string}
 */
final readonly class File
{
    public string $content;

    public string $name;

    public int $contentLength;

    public string $contentType;

    public string $contentMD5;

    public string $lastModified;

    public string $acceptRanges;

    public string $etag;

    public string $vary;

    public string $server;

    public string $xMsRequestId;

    public DateTimeImmutable $xMsVersion;

    public DateTimeImmutable $xMsCreationTime;

    public string $xMsLeaseStatus;

    public string $xMsLeaseState;

    public string $xMsBlobType;

    public bool $xMsServerEncrypted;

    public DateTimeImmutable $date;

    /** @param FileType $options */
    public function __construct(string $name, string $content, array $options = [])
    {
        // if (empty($content)) {

        // }

        $this->content = $content;
        $this->name    = $name;

        $this->contentLength      = (int) ($options['Content-Length'] ?? strlen($this->content));
        $this->contentType        = $options['Content-Type'] ?? $this->detectContentType();
        $this->contentMD5         = $options['Content-MD5'] ?? base64_encode(md5($this->content, binary: true));
        $this->lastModified       = $options['Last-Modified'] ?? '';
        $this->acceptRanges       = $options['Accept-Ranges'] ?? '';
        $this->etag               = $options['ETag'] ?? '';
        $this->vary               = $options['Vary'] ?? '';
        $this->server             = $options['Server'] ?? '';
        $this->xMsRequestId       = $options['x-ms-request-id'] ?? '';
        $this->xMsVersion         = new DateTimeImmutable($options['x-ms-version'] ?? 'now');
        $this->xMsCreationTime    = new DateTimeImmutable($options['x-ms-creation-time'] ?? 'now');
        $this->xMsLeaseStatus     = $options['x-ms-lease-status'] ?? '';
        $this->xMsLeaseState      = $options['x-ms-lease-state'] ?? '';
        $this->xMsBlobType        = $options['x-ms-blob-type'] ?? '';
        $this->xMsServerEncrypted = to_boolean($options['x-ms-server-encrypted'] ?? true);
        $this->date               = new DateTimeImmutable($options['Date'] ?? 'now');
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

    private function detectContentType(): string
    {
        $file = tmpfile();
        fwrite($file, $this->content);

        $mimeType = mime_content_type($file);

        fclose($file);

        if (!$mimeType) {
            throw InvalidFileMimeTypeException::create();
        }

        return $mimeType;
    }
}
