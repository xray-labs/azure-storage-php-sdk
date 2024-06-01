<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Http;

use Psr\Http\Message\ResponseInterface;

final class Response
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

    public function getHeaders(): array
    {
        return $this->response->getHeaders();
    }

    public function getBody(): string
    {
        return $this->response->getBody()->getContents();
    }

    public function isOk(): bool
    {
        return $this->response->getStatusCode() === self::STATUS_OK;
    }

    public function isCreated(): bool
    {
        return $this->response->getStatusCode() === self::STATUS_CREATED;
    }

    public function isAccepted(): bool
    {
        return $this->response->getStatusCode() === self::STATUS_ACCEPTED;
    }
}
