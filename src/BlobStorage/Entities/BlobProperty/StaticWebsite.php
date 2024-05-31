<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

final readonly class StaticWebsite
{
    public bool $enabled;

    public string $indexDocument;

    public string $defaultIndexDocumentPath;

    public string $errorDocument404Path;

    /**
     * Undocumented function
     *
     * @param array<string> $staticWebsite
     */
    public function __construct(array $staticWebsite)
    {
        $this->enabled                  = to_boolean($staticWebsite['Enabled'] ?? false);
        $this->indexDocument            = $staticWebsite['IndexDocument'] ?? '';
        $this->defaultIndexDocumentPath = $staticWebsite['DefaultIndexDocumentPath'] ?? '';
        $this->errorDocument404Path     = $staticWebsite['ErrorDocument404Path'] ?? '';
    }
}
