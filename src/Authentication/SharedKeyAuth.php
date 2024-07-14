<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Authentication;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Sjpereira\AzureStoragePhpSdk\Http\Headers;

final class SharedKeyAuth implements Auth
{
    public function __construct(protected string $account, protected string $key)
    {
        //
    }

    public function getDate(): string
    {
        return gmdate('D, d M Y H:i:s T');
    }

    public function getAccount(): string
    {
        return $this->account;
    }

    public function getAuthentication(
        HttpVerb $verb,
        Headers $headers,
        string $resource,
    ): string {
        $key = base64_decode($this->key);

        $stringToSign = $this->getSigningString(
            $verb->value,
            $headers->toString(),
            $headers->getCanonicalHeaders(),
            $resource,
        );

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $key, true));

        return "SharedKey {$this->account}:{$signature}";
    }

    protected function getSigningString(string $verb, string $headers, string $canonicalHeaders, string $resource): string
    {
        return "{$verb}\n{$headers}\n{$canonicalHeaders}\n/{$this->account}{$resource}";
    }
}
