<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Authentication\Contracts;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\Http\Headers;

interface Auth
{
    public function getDate(): string;

    public function getAuthentication(
        HttpVerb $verb,
        Headers $headers,
        string $resource,
    ): string;
}
