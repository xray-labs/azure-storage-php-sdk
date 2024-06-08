<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts\Http;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;

interface Request
{
    public function getConfig(): Config;

    public function withAuthentication(bool $shouldAuthenticate = true): static;

    public function withoutAuthentication(): static;

    /** @param array<string, scalar> $options */
    public function withOptions(array $options = []): static;

    /** @param array<string, scalar> $headers */
    public function withHeaders(array $headers = []): static;

    public function get(string $endpoint): Response;

    public function put(string $endpoint, string $body = ''): Response;

    public function delete(string $endpoint): Response;

    public function options(string $endpoint): Response;
}
