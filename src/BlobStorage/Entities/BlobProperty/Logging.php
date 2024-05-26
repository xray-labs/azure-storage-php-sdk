<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

readonly class Logging
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
        $this->delete                 = boolval($logging['Delete'] ?? false);
        $this->read                   = boolval($logging['Read'] ?? false);
        $this->write                  = boolval($logging['Write'] ?? false);
        $this->retentionPolicyEnabled = boolval($logging['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = (int) ($logging['RetentionPolicy']['Days'] ?? 0);
    }
}
