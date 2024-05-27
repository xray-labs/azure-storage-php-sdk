<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

readonly class MinuteMetrics
{
    public string $version;

    public bool $enabled;

    public bool $includeAPIs;

    public bool $retentionPolicyEnabled;

    public int $retentionPolicyDays;

    public function __construct(array $minuteMetricts)
    {
        $this->version                = $minuteMetricts['Version'] ?? '';
        $this->enabled                = boolval($minuteMetricts['Enabled'] ?? false);
        $this->includeAPIs            = boolval($minuteMetricts['IncludeAPIs'] ?? false);
        $this->retentionPolicyEnabled = boolval($minuteMetricts['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = (int) ($minuteMetricts['RetentionPolicy']['Days'] ?? 0);
    }
}
