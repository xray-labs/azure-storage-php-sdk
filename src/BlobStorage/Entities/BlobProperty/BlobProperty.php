<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

readonly class BlobProperty
{
    public string $defaultServiceVersion;

    public Logging $logging;

    public HourMetrics $hourMetrics;

    public MinuteMetrics $minuteMetrics;

    public CorsRules $corsRules;

    public DeleteRetentionPolicy $deleteRetentionPolicy;

    public StaticWebsite $staticWebsite;

    public function __construct(array $blobProperty)
    {
        $this->defaultServiceVersion = $blobProperty['DefaultServiceVersion'] ?? '';

        $this->logging = new Logging($blobProperty['Logging'] ?? []);

        $this->hourMetrics = new HourMetrics($blobProperty['HourMetrics'] ?? []);

        $this->minuteMetrics = new MinuteMetrics($blobProperty['MinuteMetrics'] ?? []);

        $this->corsRules = new CorsRules($blobProperty['Cors']['CorsRule'] ?? []);

        $this->deleteRetentionPolicy = new DeleteRetentionPolicy($blobProperty['DeleteRetentionPolicy'] ?? []);

        $this->staticWebsite = new StaticWebsite($blobProperty['StaticWebsite'] ?? []);
    }
}
