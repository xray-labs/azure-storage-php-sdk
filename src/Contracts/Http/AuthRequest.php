<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Http;

use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;

interface AuthRequest
{
    public function getAuth(): Auth;

    public function withAuthentication(bool $shouldAuthenticate = true): static;

    public function withoutAuthentication(): static;
}
