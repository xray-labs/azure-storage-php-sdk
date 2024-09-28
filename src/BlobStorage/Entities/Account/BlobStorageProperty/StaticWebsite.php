<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty;

use Xray\AzureStoragePhpSdk\Contracts\Arrayable;

use function Xray\AzureStoragePhpSdk\Support\to_boolean;

/**
 * @phpstan-type StaticWebsiteType array{Enabled?: bool, IndexDocument?: string, DefaultIndexDocumentPath?: string, ErrorDocument404Path?: string}
 *
 * @implements Arrayable<array{StaticWebsite: StaticWebsiteType}>
 */
final readonly class StaticWebsite implements Arrayable
{
    public bool $enabled;

    public string $indexDocument;

    public string $defaultIndexDocumentPath;

    public string $errorDocument404Path;

    /** @param StaticWebsiteType $staticWebsite */
    public function __construct(array $staticWebsite)
    {
        $this->enabled                  = to_boolean($staticWebsite['Enabled'] ?? false);
        $this->indexDocument            = $staticWebsite['IndexDocument'] ?? '';
        $this->defaultIndexDocumentPath = $staticWebsite['DefaultIndexDocumentPath'] ?? '';
        $this->errorDocument404Path     = $staticWebsite['ErrorDocument404Path'] ?? '';
    }

    public function toArray(): array
    {
        return [
            'StaticWebsite' => [
                'Enabled'                  => $this->enabled,
                'IndexDocument'            => $this->indexDocument,
                'DefaultIndexDocumentPath' => $this->defaultIndexDocumentPath,
                'ErrorDocument404Path'     => $this->errorDocument404Path,
            ],
        ];
    }
}
