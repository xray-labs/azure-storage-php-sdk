<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

readonly class StaticWebsite
{
    public bool $enabled;

    public string $indexDocument;

    public string $defaultIndexDocumentPath;

    public string $errorDocument404Path;

    public function __construct(array $staticWebsite)
    {
        $this->enabled                  = boolval($staticWebsite['Enabled'] ?? false);
        $this->indexDocument            = $staticWebsite['IndexDocument'] ?? '';
        $this->defaultIndexDocumentPath = $staticWebsite['DefaultIndexDocumentPath'] ?? '';
        $this->errorDocument404Path     = $staticWebsite['ErrorDocument404Path'] ?? '';
    }
}
