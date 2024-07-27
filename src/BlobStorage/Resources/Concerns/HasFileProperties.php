<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Resources\Concerns;

use DateTimeImmutable;

trait HasFileProperties
{
    protected string $content;

    protected string $name;

    protected int $contentLength;

    protected string $contentType;

    protected string $contentMD5;

    protected DateTimeImmutable $lastModified;

    protected string $acceptRanges;

    protected string $eTag;

    protected string $vary;

    protected string $server;

    protected string $xMsRequestId;

    protected string $xMsVersion;

    protected DateTimeImmutable $xMsCreationTime;

    protected string $xMsLeaseStatus;

    protected string $xMsLeaseState;

    protected string $xMsBlobType;

    protected bool $xMsServerEncrypted;

    protected DateTimeImmutable $date;
}
