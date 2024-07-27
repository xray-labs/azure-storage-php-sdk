<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account;

use DateTimeImmutable;

/**
 * @phpstan-type GeoReplicationType array{Status: string, LastSyncTime: string}
 */
final readonly class GeoReplication
{
    public string $status;

    public DateTimeImmutable $lastSyncTime;

    /** @param GeoReplicationType $geoReplication */
    public function __construct(array $geoReplication)
    {
        $this->status       = $geoReplication['Status'];
        $this->lastSyncTime = new DateTimeImmutable($geoReplication['LastSyncTime']);
    }
}
