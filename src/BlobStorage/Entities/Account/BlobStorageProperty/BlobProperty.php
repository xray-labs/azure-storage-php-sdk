<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\Cors\Cors;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Arrayable, Xmlable};
use Sjpereira\AzureStoragePhpSdk\Converter\XmlConverter;

/**
 * @phpstan-import-type LoggingType from Logging
 * @phpstan-import-type HourMetricsType from HourMetrics
 * @phpstan-import-type MinuteMetricsType from MinuteMetrics
 * @phpstan-import-type CorsType from Cors
 * @phpstan-import-type DeleteRetentionPolicyType from DeleteRetentionPolicy
 * @phpstan-import-type StaticWebsiteType from StaticWebsite
 *
 * @phpstan-type BlobPropertyType array{DefaultServiceVersion?: string, Logging?: LoggingType, HourMetrics?: HourMetricsType, MinuteMetrics?: MinuteMetricsType, Cors?: CorsType, DeleteRetentionPolicy?: DeleteRetentionPolicyType, StaticWebsite?: StaticWebsiteType}
 *
 * @implements Arrayable<array{StorageServiceProperties: array{DefaultServiceVersion: string, Logging?: LoggingType, HourMetrics?: HourMetricsType, MinuteMetrics?: MinuteMetricsType, Cors?: CorsType, DeleteRetentionPolicy?: DeleteRetentionPolicyType, StaticWebsite?: StaticWebsiteType}}>
 */
final readonly class BlobProperty implements Arrayable, Xmlable
{
    public string $defaultServiceVersion;

    public ?Logging $logging;

    public ?HourMetrics $hourMetrics;

    public ?MinuteMetrics $minuteMetrics;

    public ?Cors $cors;

    public ?DeleteRetentionPolicy $deleteRetentionPolicy;

    public ?StaticWebsite $staticWebsite;

    /** @param BlobPropertyType $blobProperty */
    public function __construct(array $blobProperty)
    {
        $this->defaultServiceVersion = $blobProperty['DefaultServiceVersion'] ?? '';

        $this->logging = isset($blobProperty['Logging'])
            ? new Logging($blobProperty['Logging'])
            : null;

        $this->hourMetrics = isset($blobProperty['HourMetrics'])
            ? new HourMetrics($blobProperty['HourMetrics'])
            : null;

        $this->minuteMetrics = isset($blobProperty['MinuteMetrics'])
            ? new MinuteMetrics($blobProperty['MinuteMetrics'])
            : null;

        if (isset($blobProperty['Cors'])) {
            $this->cors = isset($blobProperty['Cors']['CorsRule'])
                ? new Cors($blobProperty['Cors']['CorsRule'])
                : new Cors([]);
        } else {
            $this->cors = null;
        }

        $this->deleteRetentionPolicy = isset($blobProperty['DeleteRetentionPolicy'])
            ? new DeleteRetentionPolicy($blobProperty['DeleteRetentionPolicy'])
            : null;

        $this->staticWebsite = isset($blobProperty['StaticWebsite'])
            ? new StaticWebsite($blobProperty['StaticWebsite'])
            : null;
    }

    public function toArray(): array
    {
        $values = ['DefaultServiceVersion' => $this->defaultServiceVersion];

        if ($this->logging) {
            $values[] = $this->logging->toArray();
        }

        if ($this->hourMetrics) {
            $values[] = $this->hourMetrics->toArray();
        }

        if ($this->minuteMetrics) {
            $values[] = $this->minuteMetrics->toArray();
        }

        if ($this->cors) {
            $values[] = $this->cors->toArray();
        }

        if ($this->deleteRetentionPolicy) {
            $values[] = $this->deleteRetentionPolicy->toArray();
        }

        if ($this->staticWebsite) {
            $values[] = $this->staticWebsite->toArray();
        }

        return ['StorageServiceProperties' => array_filter(
            $values,
            fn (mixed $value) => $value !== null,
        )];
    }

    public function toXml(): string
    {
        return (new XmlConverter())->convert($this->toArray());
    }
}
