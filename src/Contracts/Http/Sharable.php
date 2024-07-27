<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Http;

use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Http\Headers;

interface Sharable
{
    public function getVerb(): HttpVerb;

    public function getBody(): string;

    public function getResource(): string;

    public function getHttpHeaders(): Headers;
}
