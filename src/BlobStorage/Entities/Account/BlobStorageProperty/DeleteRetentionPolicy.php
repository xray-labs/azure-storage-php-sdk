<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;

/**
 * @phpstan-type DeleteRetentionPolicyType array{Enabled?: bool, AllowPermanentDelete?: bool, Days?: int}
 *
 * @implements Arrayable<array{DeleteRetentionPolicy: DeleteRetentionPolicyType}>
 */
final readonly class DeleteRetentionPolicy implements Arrayable
{
    public bool $enabled;

    public bool $allowPermanentDelete;

    public ?int $days;

    /** @param DeleteRetentionPolicyType $deleteRetentionPolicy */
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
