<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

final readonly class DeleteRetentionPolicy
{
    public bool $enabled;

    public bool $allowPermanentDelete;

    public int $days;

    public function __construct(array $deleteRetentionPolicy)
    {
        $this->enabled              = to_boolean($deleteRetentionPolicy['Enabled'] ?? false);
        $this->allowPermanentDelete = to_boolean($deleteRetentionPolicy['AllowPermanentDelete'] ?? false);
        $this->days                 = (int) ($deleteRetentionPolicy['Days'] ?? 0);
    }
}
