<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Authentication;

use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Http\Headers;

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
