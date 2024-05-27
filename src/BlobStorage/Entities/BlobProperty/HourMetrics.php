<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

final readonly class HourMetrics
{
    public string $version;

    public bool $enabled;

    public bool $includeAPIs;

    public bool $retentionPolicyEnabled;

    public int $retentionPolicyDays;

    public function __construct(array $hourMetrics)
    {
        $this->version                = $hourMetrics['Version'] ?? '';
        $this->enabled                = to_boolean($hourMetrics['Enabled'] ?? false);
        $this->includeAPIs            = to_boolean($hourMetrics['IncludeAPIs'] ?? false);
        $this->retentionPolicyEnabled = to_boolean($hourMetrics['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = (int) ($hourMetrics['RetentionPolicy']['Days'] ?? 0);
    }
}
