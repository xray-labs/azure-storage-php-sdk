<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\AccessTokenPermission;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

final readonly class UserDelegationKey
{
    public string $signedOid;

    public string $signedTid;

    public DateTimeImmutable $signedStart;

    public DateTimeImmutable $signedExpiry;

    public string $signedService;

    public string $signedVersion;

    public string $value;

    /** @param array{SignedOid: string, SignedTid: string, SignedStart: string, SignedExpiry: string, SignedService: string, SignedVersion: string, Value: string} $userDelegationKey */
    public function __construct(array $userDelegationKey)
    {
        $this->signedOid     = $userDelegationKey['SignedOid'];
        $this->signedTid     = $userDelegationKey['SignedTid'];
        $this->signedStart   = new DateTimeImmutable($userDelegationKey['SignedStart']);
        $this->signedExpiry  = new DateTimeImmutable($userDelegationKey['SignedExpiry']);
        $this->signedService = $userDelegationKey['SignedService'];
        $this->signedVersion = $userDelegationKey['SignedVersion'];
        $this->value         = $userDelegationKey['Value'];
    }

    /** @throws InvalidArgumentException */
    public function generateTokenUrl(string $uri, AccessTokenPermission $permission): string
    {
        if (filter_var($uri, FILTER_VALIDATE_URL) === false) {
            throw InvalidArgumentException::create('The uri is not a valid url.');
        }

        $protocol = parse_url($uri, PHP_URL_SCHEME) ?? 'https';
        // $resource = "/blob/{$this->accountName}/{$containerName}/{$blobName}";

        /**
         * sp=r
         * st=2024-08-03T20:21:13Z
         * se=2024-08-04T04:21:13Z
         * skoid=1db31684-3017-4faa-a15b-b09f1a9ee75c
         * sktid=0a20a1a3-567e-45a2-8c3a-3300a41c8770
         * skt=2024-08-03T20:21:13Z
         * ske=2024-08-04T04:21:13Z
         * sks=b
         * skv=2022-11-02
         * spr=https
         * sv=2022-11-02
         * sr=b
         * sig=dtuPFgDb%2BXWJWsii9BwBlaJWe5FMED0LXbcDTRVu308%3D
         */
        $parameters = [
            'sp'    => $permission->value,
            'st'    => convert_to_ISO($this->signedStart),
            'se'    => convert_to_ISO($this->signedExpiry),
            'skoid' => $this->signedOid,
            'sktid' => $this->signedTid,
            'skt'   => convert_to_ISO($this->signedStart),
            'ske'   => convert_to_ISO($this->signedExpiry),
            'sks'   => $this->signedService,
            'skv'   => $this->signedVersion,
            'spr'   => $protocol,
            'sv'    => $this->signedVersion,
            'sr'    => $this->signedService,

            'sig' => urlencode($this->value),
        ];

        // dd(base64_decode($this->value));

        // $signature    = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($this->value), true));

        // unset($parameters['canonicalizeResource']);

        $queryString = '';

        foreach ($parameters as $key => $value) {
            $queryString .= "{$key}=" . $value . '&';
        }

        // $queryString = http_build_query([
        //     ...$parameters,
        //     'sig' => $this->value,
        // ]);

        return $uri . rtrim($queryString, '&');
    }
}
