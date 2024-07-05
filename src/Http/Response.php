<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Http;

use Psr\Http\Message\ResponseInterface;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Response as ResponseContract;

final class Response implements ResponseContract
{
    public const int STATUS_OK       = 200;
    public const int STATUS_CREATED  = 201;
    public const int STATUS_ACCEPTED = 202;

    public function __construct(protected ResponseInterface $response)
    {
        //
    }

    public static function createFromGuzzleResponse(ResponseInterface $response): self
    {
        return new self($response);
    }

    /** @return array<string, mixed> */
    public function getHeaders(): array
    {
        $headers = [];

        foreach ($this->response->getHeaders() as $name => $values) {
            $headers[$name] = current($values);
        }

        return $headers;
    }

    /** @return array<string, mixed> */
    public function getHeaderLine(string $header): array
    {
        return $this->response->getHeader($header);
    }

    public function getBody(): string
    {
        return $this->response->getBody()->getContents();
    }

    public function getStatusCode(): int
    {
        return $this->response->getStatusCode();
    }

    public function isOk(): bool
    {
        return $this->getStatusCode() === self::STATUS_OK;
    }

    public function isCreated(): bool
    {
        return $this->getStatusCode() === self::STATUS_CREATED;
    }

    public function isAccepted(): bool
    {
        return $this->getStatusCode() === self::STATUS_ACCEPTED;
    }
}
