<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Concerns;

use DateTimeImmutable;

trait HasFileMethods
{
    public function getContent(): string
    {
        return $this->content;
    }

    public function getFilename(): string
    {
        return $this->name;
    }

    public function getContentLength(): int
    {
        return $this->contentLength;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getContentMD5(): string
    {
        return $this->contentMD5;
    }

    public function getLastModified(): DateTimeImmutable
    {
        return $this->lastModified;
    }

    public function getAcceptRanges(): string
    {
        return $this->acceptRanges;
    }

    public function getETag(): string
    {
        return $this->eTag;
    }

    public function getVary(): string
    {
        return $this->vary;
    }

    public function getServer(): string
    {
        return $this->server;
    }

    public function getRequestId(): string
    {
        return $this->xMsRequestId;
    }

    public function getVersion(): string
    {
        return $this->xMsVersion;
    }

    public function getCreationTime(): DateTimeImmutable
    {
        return $this->xMsCreationTime;
    }

    public function getLeaseStatus(): string
    {
        return $this->xMsLeaseStatus;
    }

    public function getLeaseState(): string
    {
        return $this->xMsLeaseState;
    }

    public function getBlobType(): string
    {
        return $this->xMsBlobType;
    }

    public function getServerEncrypted(): bool
    {
        return $this->xMsServerEncrypted;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }
}
