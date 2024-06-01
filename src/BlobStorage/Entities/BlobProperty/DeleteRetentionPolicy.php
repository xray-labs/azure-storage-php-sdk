<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;

final readonly class DeleteRetentionPolicy implements Arrayable
{
    public bool $enabled;

    public bool $allowPermanentDelete;

    public ?int $days;

    /**
     * @param array{
     *  Enabled: ?bool,
     *  AllowPermanentDelete: ?bool,
     *  Days: ?int
     * } $deleteRetentionPolicy
     */
    public function __construct(array $deleteRetentionPolicy)
    {
        $this->enabled              = to_boolean($deleteRetentionPolicy['Enabled'] ?? false);
        $this->allowPermanentDelete = to_boolean($deleteRetentionPolicy['AllowPermanentDelete'] ?? false);
        $this->days                 = isset($deleteRetentionPolicy['Days'])
            ? (int) $deleteRetentionPolicy['Days']
            : null;
    }

    public function toArray(): array
    {
        return [
            'DeleteRetentionPolicy' => array_filter([
                'Enabled'              => $this->enabled,
                'AllowPermanentDelete' => $this->allowPermanentDelete,
                'Days'                 => $this->days,
            ], fn (bool|int|null $value) => $value !== null && $value !== ''),
        ];
    }
}
