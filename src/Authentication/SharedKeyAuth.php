<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Authentication;

use Sjpereira\AzureStoragePhpSdk\Authentication\Contracts\Auth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
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
            $resource,
            $headers->additionalHeaders
        );

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $key, true));

        return "SharedKey {$this->config->account}:{$signature}";
    }

    protected function getSigningString(string $verb, string $headers, string $resource, array $additionalHeaders = []): string
    {
        $canonicalizedAdditionalHeaders = $this->getCanonicalizedHeaders(array_merge($additionalHeaders, [
            Resource::AUTH_DATE_KEY    => $this->getDate(),
            Resource::AUTH_VERSION_KEY => $this->config->version,
        ]));

        return "{$verb}\n{$headers}\n{$canonicalizedAdditionalHeaders}\n/{$this->config->account}{$resource}";
    }

    protected function getCanonicalizedHeaders($headers)
    {
        $x_ms_headers = [];

        foreach ($headers as $key => $value) {
            $key_lower = strtolower($key);

            if (strpos($key_lower, 'x-ms-') === 0) {
                $x_ms_headers[$key_lower] = $value;
            }
        }

        ksort($x_ms_headers);

        $canonicalizedHeaders = '';

        foreach ($x_ms_headers as $key => $value) {
            $canonicalizedHeaders .= $key . ':' . $value . "\n";
        }

        return rtrim($canonicalizedHeaders, "\n");
    }
}
