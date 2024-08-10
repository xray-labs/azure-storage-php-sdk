<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\Cors\Cors;
use Xray\AzureStoragePhpSdk\Contracts\{Arrayable, Xmlable};
use Xray\AzureStoragePhpSdk\Converter\XmlConverter;

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
            ? azure_app(Logging::class, ['logging' => $blobProperty['Logging']])
            : null; // @codeCoverageIgnore

        $this->hourMetrics = isset($blobProperty['HourMetrics'])
            ? azure_app(HourMetrics::class, ['hourMetrics' => $blobProperty['HourMetrics']])
            : null; // @codeCoverageIgnore

        $this->minuteMetrics = isset($blobProperty['MinuteMetrics'])
            ? azure_app(MinuteMetrics::class, ['minuteMetrics' => $blobProperty['MinuteMetrics']])
            : null; // @codeCoverageIgnore

        if (isset($blobProperty['Cors'])) {
            $this->cors = azure_app(Cors::class, isset($blobProperty['Cors']['CorsRule']) ? ['corsRules' => $blobProperty['Cors']['CorsRule']] : []);
        } else {
            $this->cors = null; // @codeCoverageIgnore
        }

        $this->deleteRetentionPolicy = isset($blobProperty['DeleteRetentionPolicy'])
            ? azure_app(DeleteRetentionPolicy::class, ['deleteRetentionPolicy' => $blobProperty['DeleteRetentionPolicy']])
            : null; // @codeCoverageIgnore

        $this->staticWebsite = isset($blobProperty['StaticWebsite'])
            ? azure_app(StaticWebsite::class, ['staticWebsite' => $blobProperty['StaticWebsite']])
            : null; // @codeCoverageIgnore
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
        return azure_app(XmlConverter::class)->convert($this->toArray());
    }
}
