<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Xray\AzureStoragePhpSdk\Contracts\Arrayable;

use function Xray\AzureStoragePhpSdk\Support\to_boolean;

/**
 * @phpstan-type RetentionPolicyType array{Days?: int, Enabled: bool}
 * @phpstan-type HourMetricsType array{Version: ?string, Enabled?: bool, IncludeAPIs?: bool, RetentionPolicy?: RetentionPolicyType}
 *
 * @implements Arrayable<array{HourMetrics: HourMetricsType}>
 */
final readonly class HourMetrics implements Arrayable
{
    public string $version;

    public bool $enabled;

    public bool $includeAPIs;

    public bool $retentionPolicyEnabled;

    public ?int $retentionPolicyDays;

    /** @param HourMetricsType $hourMetrics */
    public function __construct(array $hourMetrics)
    {
        $this->version                = $hourMetrics['Version'] ?? '';
        $this->enabled                = to_boolean($hourMetrics['Enabled'] ?? false);
        $this->includeAPIs            = to_boolean($hourMetrics['IncludeAPIs'] ?? false);
        $this->retentionPolicyEnabled = to_boolean($hourMetrics['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = isset($hourMetrics['RetentionPolicy']['Days'])
            ? (int) $hourMetrics['RetentionPolicy']['Days']
            : null; // @codeCoverageIgnore
    }

    public function toArray(): array
    {
        return [
            'HourMetrics' => [
                'Version'         => $this->version,
                'Enabled'         => $this->enabled,
                'IncludeAPIs'     => $this->includeAPIs,
                'RetentionPolicy' => array_filter([
                    'Enabled' => $this->retentionPolicyEnabled,
                    'Days'    => $this->retentionPolicyDays,
                ], fn (bool|int|null $value) => $value !== null && $value !== ''),
            ],
        ];
    }
}
