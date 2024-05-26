<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

readonly class HourMetrics
{
    public string $version;

    public bool $enabled;

    public bool $includeAPIs;

    public bool $retentionPolicyEnabled;

    public int $retentionPolicyDays;

    public function __construct(array $hourMetricts)
    {
        $this->version                = $hourMetricts['Version'] ?? '';
        $this->enabled                = boolval($hourMetricts['Enabled'] ?? false);
        $this->includeAPIs            = boolval($hourMetricts['IncludeAPIs'] ?? false);
        $this->retentionPolicyEnabled = boolval($hourMetricts['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = (int) ($hourMetricts['RetentionPolicy']['Days'] ?? 0);
    }
}
