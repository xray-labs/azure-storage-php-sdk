<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Http;

use Closure;
use Xray\AzureStoragePhpSdk\Contracts\HasConfig;

interface Request extends HasConfig, HttpMethods
{
    public function usingAccount(Closure $callback): static;

    public function withAuthentication(bool $shouldAuthenticate = true): static;

    public function withoutAuthentication(): static;

    /** @param array<string, scalar> $options */
    public function withOptions(array $options = []): static;

    /** @param array<string, scalar> $headers */
    public function withHeaders(array $headers = []): static;

    public function uri(?string $endpoint = null): string;
}
