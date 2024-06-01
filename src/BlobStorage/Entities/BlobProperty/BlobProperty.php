<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty\Cors\Cors;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Arrayable, Xmlable};
use Sjpereira\AzureStoragePhpSdk\Converter\XmlConverter;

final readonly class BlobProperty implements Arrayable, Xmlable
{
    public string $defaultServiceVersion;

    public ?Logging $logging;

    public ?HourMetrics $hourMetrics;

    public ?MinuteMetrics $minuteMetrics;

    public ?Cors $cors;

    public ?DeleteRetentionPolicy $deleteRetentionPolicy;

    public ?StaticWebsite $staticWebsite;

    /** @param array{
     *  DefaultServiceVersion: string,
     *  Logging?: array{
     *      Version: ?string,
     *      Delete: ?bool,
     *      Read: ?bool,
     *      Write: ?bool,
     *      RetentionPolicy: ?array{
     *          Days?: int,
     *          Enabled: bool
     *      }
     *  },
     *  HourMetrics?: array{
     *      Version: ?string,
     *      Enabled: ?bool,
     *      IncludeAPIs: ?bool,
     *      RetentionPolicy: ?array{
     *          Days?: int,
     *          Enabled: bool
     *      }
     *  },
     *  MinuteMetrics?: array{
     *      Version: ?string,
     *      Enabled: ?bool,
     *      IncludeAPIs: ?bool,
     *      RetentionPolicy: ?array{
     *          Days?: int,
     *          Enabled: bool
     *      }
     *  },
     *  Cors?: array{
     *      CorsRules: array{
     *          AllowedOrigins?: string,
     *          AllowedMethods?: string,
     *          MaxAgeInSeconds?: int,
     *          ExposedHeaders?: string,
     *          AllowedHeaders?: string,
     *  }[],
     *  DeleteRetentionPolicy?: array{
     *     Enabled: ?bool,
     *     AllowPermanentDelete: ?bool,
     *     Days?: int
     *  },
     *  StaticWebsite?: array{
     *      Enabled: bool,
     *      IndexDocument: string,
     *      DefaultIndexDocumentPath: string,
     *      ErrorDocument404Path: string,
     *  }
     * } $blobProperty
     */
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

        dd($blobProperty);

        $this->cors = isset($blobProperty['CorsRules'])
            ? new Cors($blobProperty['CorsRules'])
            : null;

        $this->deleteRetentionPolicy = isset($blobProperty['DeleteRetentionPolicy'])
            ? new DeleteRetentionPolicy($blobProperty['DeleteRetentionPolicy'])
            : null;

        $this->staticWebsite = isset($blobProperty['StaticWebsite'])
            ? new StaticWebsite($blobProperty['StaticWebsite'])
            : null;
    }

    public function toArray(): array
    {
        return [
            'StorageServiceProperties' => array_filter([
                'DefaultServiceVersion' => $this->defaultServiceVersion,
                ...($this->logging?->toArray() ?? []),
                ...($this->hourMetrics?->toArray() ?? []),
                ...($this->minuteMetrics?->toArray() ?? []),
                ...($this->cors?->toArray() ?? []),
                ...($this->deleteRetentionPolicy?->toArray() ?? []),
                ...($this->staticWebsite?->toArray() ?? []),
            ], fn (mixed $value) => $value !== null),
        ];
    }

    public function toXml(): string
    {
        return (new XmlConverter())->convert($this->toArray());
    }
}
