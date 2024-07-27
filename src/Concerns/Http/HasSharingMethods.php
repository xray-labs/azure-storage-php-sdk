<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Concerns\Http;

use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Http\Headers;

trait HasSharingMethods
{
    protected HttpVerb $verb;

    protected string $body;

    protected string $resource;

    protected Headers $httpHeaders;

    public function getVerb(): HttpVerb
    {
        return $this->verb ?? HttpVerb::GET;
    }

    public function withVerb(HttpVerb $verb): static
    {
        $this->verb = $verb;

        return $this;
    }

    public function getBody(): string
    {
        return $this->body ?? '';
    }

    public function withBody(string $body): static
    {
        $this->body = $body;

        return $this;
    }

    public function getResource(): string
    {
        return $this->resource ?? '';
    }

    public function withResource(string $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function getHttpHeaders(): Headers
    {
        return $this->httpHeaders ?? new Headers();
    }

    public function withHttpHeaders(Headers $headers): static
    {
        $this->httpHeaders = $headers;

        return $this;
    }
}
