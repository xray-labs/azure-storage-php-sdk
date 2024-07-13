<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts\Http;

interface Response
{
    /** @return array<string, mixed> */
    public function getHeaders(): array;

    /** @return array<string, mixed> */
    public function getHeaderLine(string $header): array;

    public function getBody(): string;

    public function getStatusCode(): int;

    public function isOk(): bool;

    public function isCreated(): bool;

    public function isAccepted(): bool;

    public function isNoContent(): bool;
}
