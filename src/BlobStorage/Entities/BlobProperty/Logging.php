<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

final readonly class Logging
{
    public string $version;

    public bool $delete;

    public bool $read;

    public bool $write;

    public bool $retentionPolicyEnabled;

    public int $retentionPolicyDays;

    public function __construct(array $logging)
    {
        $this->version                = $logging['Version'] ?? '';
        $this->delete                 = to_boolean($logging['Delete'] ?? false);
        $this->read                   = to_boolean($logging['Read'] ?? false);
        $this->write                  = to_boolean($logging['Write'] ?? false);
        $this->retentionPolicyEnabled = to_boolean($logging['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = (int) ($logging['RetentionPolicy']['Days'] ?? 0);
    }
}
