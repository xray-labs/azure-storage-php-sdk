<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Http;

use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Http\Headers;

interface Sharable
{
    public function withVerb(HttpVerb $verb): static;

    public function getVerb(): HttpVerb;

    public function withBody(string $body): static;

    public function getBody(): string;

    public function withResource(string $resource): static;

    public function getResource(): string;

    public function withHttpHeaders(Headers $headers): static;

    public function getHttpHeaders(): Headers;
}
