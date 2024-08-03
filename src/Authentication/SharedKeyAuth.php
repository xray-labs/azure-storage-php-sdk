<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Authentication;

use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;

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

    public function getAuthentication(Request $request): string
    {
        $key = base64_decode($this->key);

        $stringToSign = $this->getSigningString(
            $request->getVerb()->value,
            $request->getHttpHeaders()->toString(),
            $request->getHttpHeaders()->getCanonicalHeaders(),
            $request->getResource(),
        );

        $signature = base64_encode(hash_hmac('sha256', $stringToSign, $key, true));

        return "SharedKey {$this->account}:{$signature}";
    }

    protected function getSigningString(string $verb, string $headers, string $canonicalHeaders, string $resource): string
    {
        return "{$verb}\n{$headers}\n{$canonicalHeaders}\n/{$this->account}{$resource}";
    }
}
