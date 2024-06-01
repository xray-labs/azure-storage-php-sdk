<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;

final readonly class StaticWebsite implements Arrayable
{
    public bool $enabled;

    public string $indexDocument;

    public string $defaultIndexDocumentPath;

    public string $errorDocument404Path;

    /** @param array<string> $staticWebsite */
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
