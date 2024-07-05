<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Tests\Http;

use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Response;

class ResponseFake implements Response
{
    /** @param array<string, scalar|scalar[]> $headers */
    public function __construct(
        protected string $body = '',
        protected int $statusCode = 200,
        protected array $headers = [],
    ) {
        //
    }

    /** @return array<string, mixed> */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /** @return array<string, mixed> */
    public function getHeaderLine(string $header): array
    {
        /** @var array<string, mixed> */
        return $this->headers[$header] ?? [];
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function isOk(): bool
    {
        return $this->getStatusCode() === 200;
    }

    public function isCreated(): bool
    {
        return $this->getStatusCode() === 201;
    }

    public function isAccepted(): bool
    {
        return $this->getStatusCode() === 202;
    }
}
