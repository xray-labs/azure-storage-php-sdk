<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Tests\Http;

use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Response;

class ResponseFake implements Response
{
    /** @return array<string, mixed> */
    public function getHeaders(): array
    {
        return [];
    }

    public function getBody(): string
    {
        return '';
    }

    public function getStatusCode(): int
    {
        return 200;
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
