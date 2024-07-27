<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Xray\AzureStoragePhpSdk\Contracts\Arrayable;

/**
 * @phpstan-type RetentionPolicyType array{Days?: int, Enabled: bool}
 * @phpstan-type LoggingType array{Version: ?string, Delete?: bool, Read?: bool, Write?: bool, RetentionPolicy?: RetentionPolicyType}
 *
 * @implements Arrayable<array{Logging: LoggingType}>
 */
final readonly class Logging implements Arrayable
{
    public string $version;

    public bool $delete;

    public bool $read;

    public bool $write;

    public bool $retentionPolicyEnabled;

    public ?int $retentionPolicyDays;

    /** @param LoggingType $logging */
    public function __construct(array $logging)
    {
        $this->version                = $logging['Version'] ?? '';
        $this->delete                 = to_boolean($logging['Delete'] ?? false);
        $this->read                   = to_boolean($logging['Read'] ?? false);
        $this->write                  = to_boolean($logging['Write'] ?? false);
        $this->retentionPolicyEnabled = to_boolean($logging['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = isset($logging['RetentionPolicy']['Days'])
            ? (int) $logging['RetentionPolicy']['Days']
            : null; // @codeCoverageIgnore
    }

    public function toArray(): array
    {
        return [
            'Logging' => [
                'Version'         => $this->version,
                'Delete'          => $this->delete,
                'Read'            => $this->read,
                'Write'           => $this->write,
                'RetentionPolicy' => array_filter([
                    'Enabled' => $this->retentionPolicyEnabled,
                    'Days'    => $this->retentionPolicyDays,
                ], fn (bool|int|null $value) => $value !== null && $value !== ''),
            ],
        ];
    }
}
