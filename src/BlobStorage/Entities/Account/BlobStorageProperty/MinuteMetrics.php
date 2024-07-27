<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Xray\AzureStoragePhpSdk\Contracts\Arrayable;

/**
 * @phpstan-type RetentionPolicyType array{Days?: int, Enabled: bool}
 * @phpstan-type MinuteMetricsType array{Version: ?string, Enabled?: bool, IncludeAPIs?: bool, RetentionPolicy?: RetentionPolicyType}
 *
 * @implements Arrayable<array{MinuteMetrics: MinuteMetricsType}>
 */
final readonly class MinuteMetrics implements Arrayable
{
    public string $version;

    public bool $enabled;

    public bool $includeAPIs;

    public bool $retentionPolicyEnabled;

    public ?int $retentionPolicyDays;

    /** @param MinuteMetricsType $minuteMetrics */
    public function __construct(array $minuteMetrics)
    {
        $this->version                = $minuteMetrics['Version'] ?? '';
        $this->enabled                = to_boolean($minuteMetrics['Enabled'] ?? false);
        $this->includeAPIs            = to_boolean($minuteMetrics['IncludeAPIs'] ?? false);
        $this->retentionPolicyEnabled = to_boolean($minuteMetrics['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = isset($minuteMetrics['RetentionPolicy']['Days'])
            ? (int) $minuteMetrics['RetentionPolicy']['Days']
            : null; // @codeCoverageIgnore
    }

    public function toArray(): array
    {
        return [
            'MinuteMetrics' => [
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
