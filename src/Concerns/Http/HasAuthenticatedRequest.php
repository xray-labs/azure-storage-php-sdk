<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Concerns\Http;

use Closure;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;

trait HasAuthenticatedRequest
{
    protected ?Closure $usingAccountCallback = null;

    protected bool $shouldAuthenticate = true;

    public function getAuth(): Auth
    {
        return $this->auth;
    }

    public function withAuthentication(bool $shouldAuthenticate = true): static
    {
        $this->shouldAuthenticate = $shouldAuthenticate;

        return $this;
    }

    public function withoutAuthentication(): static
    {
        return $this->withAuthentication(false);
    }
}
