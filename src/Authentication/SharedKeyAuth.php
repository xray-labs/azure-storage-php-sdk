<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Authentication;

use Xray\AzureStoragePhpSdk\Concerns\UseCurrentHttpDate;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

final class SharedKeyAuth implements Auth
{
    use UseCurrentHttpDate;

    protected string $account;

    protected string $key;

    /** @param array{account: string, key: string} $config */
    public function __construct(array $config)
    {
        // @phpstan-ignore-next-line
        if (!isset($config['account'], $config['key'])) {
            $missingParameters = array_diff(['account', 'key'], array_keys($config));

            throw RequiredFieldException::create('Missing required parameters: ' . implode(', ', $missingParameters));
        }

        $this->account = $config['account'];
        $this->key     = $config['key'];
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
