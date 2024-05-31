<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

final readonly class MinuteMetrics
{
    public string $version;

    public bool $enabled;

    public bool $includeAPIs;

    public bool $retentionPolicyEnabled;

    public int $retentionPolicyDays;

    /**
     * Undocumented function
     *
     * @param array{
     *  Version: ?string,
     *  Enabled: ?bool,
     *  IncludeAPIs: ?bool,
     *  RetentionPolicy: ?array{
     *    Enabled: bool,
     *    Days: int
     *  }
     * } $minuteMetrics
     */
    public function __construct(array $minuteMetrics)
    {
        $this->version                = $minuteMetrics['Version'] ?? '';
        $this->enabled                = to_boolean($minuteMetrics['Enabled'] ?? false);
        $this->includeAPIs            = to_boolean($minuteMetrics['IncludeAPIs'] ?? false);
        $this->retentionPolicyEnabled = to_boolean($minuteMetrics['RetentionPolicy']['Enabled'] ?? false);
        $this->retentionPolicyDays    = (int) ($minuteMetrics['RetentionPolicy']['Days'] ?? 0);
    }
}
