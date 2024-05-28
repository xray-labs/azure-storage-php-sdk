<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Authentication;

use Sjpereira\AzureStoragePhpSdk\Authentication\Contracts\Auth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;
use Sjpereira\AzureStoragePhpSdk\Http\Headers;

final class SharedKeyAuth implements Auth
{
    public function __construct(protected Config $config)
    {
        //
    }

    public function getDate(): string
    {
        return gmdate('D, d M Y H:i:s T');
    }

    public function getAuthentication(
        HttpVerb $verb,
        Headers $headers,
        string $resource,
    ): string {
        $key = base64_decode($this->config->key);

        $stringToSign = $this->getSigningString(
            $verb->value,
            (string)$headers,
            $headers->getCanonicalHeaders(),
            $resource,
        );

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $key, true));

        return "SharedKey {$this->config->account}:{$signature}";
    }

    protected function getSigningString(string $verb, string $headers, string $canonicalHeaders, string $resource): string
    {
        return "{$verb}\n{$headers}\n{$canonicalHeaders}\n/{$this->config->account}{$resource}";
    }
}
