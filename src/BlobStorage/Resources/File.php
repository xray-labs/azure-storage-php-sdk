<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Resources;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\Concerns\{HasFileMethods, HasFileProperties};
use Xray\AzureStoragePhpSdk\Exceptions\{
    CouldNotCreateTempFileException,
    InvalidArgumentException,
    InvalidFileMimeTypeException,
};

use function Xray\AzureStoragePhpSdk\Support\to_boolean;

/**
 * @phpstan-type FileType array{Content-Length?: string, Content-Type?: string, Content-MD5?: string, Last-Modified?: string, Accept-Ranges?: string, ETag?: string, Vary?: string, Server?: string, x-ms-request-id?: string, x-ms-version?: string, x-ms-creation-time?: string, x-ms-lease-status?: string, x-ms-lease-state?: string, x-ms-blob-type?: string, x-ms-server-encrypted?: bool, Date?: string}
 */
final class File
{
    use HasFileProperties;
    use HasFileMethods;

    /** @param FileType $options */
    public function __construct(string $name, string $content = '', array $options = [])
    {
        if (!$name) {
            throw InvalidArgumentException::create('[name] cannot be empty');
        }

        $this->name    = $name;
        $this->content = $content;

        $this->contentLength      = (int) ($options['Content-Length'] ?? strlen($this->content));
        $this->contentType        = $options['Content-Type'] ?? $this->detectContentType();
        $this->contentMD5         = $options['Content-MD5'] ?? base64_encode(md5($this->content, binary: true));
        $this->lastModified       = new DateTimeImmutable($options['Last-Modified'] ?? 'now');
        $this->acceptRanges       = $options['Accept-Ranges'] ?? '';
        $this->eTag               = $options['ETag'] ?? '';
        $this->vary               = $options['Vary'] ?? '';
        $this->server             = $options['Server'] ?? '';
        $this->xMsRequestId       = $options['x-ms-request-id'] ?? '';
        $this->xMsVersion         = $options['x-ms-version'] ?? '';
        $this->xMsCreationTime    = new DateTimeImmutable($options['x-ms-creation-time'] ?? 'now');
        $this->xMsLeaseStatus     = $options['x-ms-lease-status'] ?? '';
        $this->xMsLeaseState      = $options['x-ms-lease-state'] ?? '';
        $this->xMsBlobType        = $options['x-ms-blob-type'] ?? '';
        $this->xMsServerEncrypted = to_boolean($options['x-ms-server-encrypted'] ?? true);
        $this->date               = new DateTimeImmutable($options['Date'] ?? 'now');
    }

    protected function detectContentType(): string
    {
        if (($file = tmpfile()) === false) {
            throw CouldNotCreateTempFileException::create('Could not create temporary file'); // @codeCoverageIgnore
        }

        try {
            fwrite($file, $this->content);
            $mimeType = mime_content_type($file);
        } finally {
            fclose($file);
        }

        if (!$mimeType) {
            throw InvalidFileMimeTypeException::create(); // @codeCoverageIgnore
        }

        return $mimeType;
    }
}
