<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts\Authentication;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\Http\Headers;

interface Auth
{
    public function getDate(): string;

    public function getAccount(): string;

    public function getAuthentication(
        HttpVerb $verb,
        Headers $headers,
        string $resource,
    ): string;
}
