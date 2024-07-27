<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Tests\Http;

use Xray\AzureStoragePhpSdk\Contracts\Http\Response;
use Xray\AzureStoragePhpSdk\Http\Response as BaseResponse;

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
        return $this->getStatusCode() === BaseResponse::STATUS_OK;
    }

    public function isCreated(): bool
    {
        return $this->getStatusCode() === BaseResponse::STATUS_CREATED;
    }

    public function isAccepted(): bool
    {
        return $this->getStatusCode() === BaseResponse::STATUS_ACCEPTED;
    }

    public function isNoContent(): bool
    {
        return $this->getStatusCode() === BaseResponse::STATUS_NO_CONTENT;
    }
}
