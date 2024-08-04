<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Authentication\SharedAccessSignature;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\Authentication\MicrosoftEntraId;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\{KeyInfo, UserDelegationKey};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\AccessTokenPermission;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\AccountManager;
use Xray\AzureStoragePhpSdk\BlobStorage\SignatureResource;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\SharedAccessSignature;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Exceptions\Authentication\InvalidAuthenticationMethodException;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidResourceTypeException;

class UserDelegationSas implements SharedAccessSignature
{
    /** @var array<string, string> */
    protected const RESOURCE_MAP = [
        'b' => 'blob',
        'c' => 'container',
        'f' => 'file',
        's' => 'share',
        'd' => 'directory',
    ];

    public function __construct(protected Request $request)
    {
        if (!$this->request->getAuth() instanceof MicrosoftEntraId) {
            throw InvalidAuthenticationMethodException::create(sprintf(
                'Invalid Authentication Method. [%s] needed, but [%s] given.',
                MicrosoftEntraId::class,
                get_class($this->request->getAuth()),
            ));
        }
    }

    public function buildTokenUrl(AccessTokenPermission $permission, DateTimeImmutable $expiry): string
    {
        $account  = $this->request->getAuth()->getAccount();
        $resource = ltrim($this->request->getResource(), '/');

        $userDelegationKey = $this->getUserDelegationKey($expiry);

        if (!in_array($userDelegationKey->signedService, array_keys(static::RESOURCE_MAP))) {
            throw InvalidResourceTypeException::create(sprintf(
                'The [%s] signed service is not valid. The allowed services are [%s].',
                $userDelegationKey->signedService,
                implode(', ', array_keys(static::RESOURCE_MAP)),
            ));
        }

        $service        = static::RESOURCE_MAP[$userDelegationKey->signedService];
        $signedResource = "/{$service}/{$account}/{$resource}";
        $signedProtocol = parse_url($this->request->uri(), PHP_URL_SCHEME) ?? 'https';

        $parameters = [
            SignatureResource::SIGNED_PERMISSION             => $permission->value,
            SignatureResource::SIGNED_START                  => convert_to_ISO($userDelegationKey->signedStart),
            SignatureResource::SIGNED_EXPIRY                 => convert_to_ISO($userDelegationKey->signedExpiry),
            SignatureResource::SIGNED_CANONICAL_RESOURCE     => $signedResource,
            SignatureResource::SIGNED_OBJECT_ID              => $userDelegationKey->signedOid,
            SignatureResource::SIGNED_TENANT_ID              => $userDelegationKey->signedTid,
            SignatureResource::SIGNED_KEY_START_TIME         => convert_to_ISO($userDelegationKey->signedStart),
            SignatureResource::SIGNED_KEY_EXPIRY_TIME        => convert_to_ISO($userDelegationKey->signedExpiry),
            SignatureResource::SIGNED_KEY_SERVICE            => $userDelegationKey->signedService,
            SignatureResource::SIGNED_KEY_VERSION            => $userDelegationKey->signedVersion,
            SignatureResource::SIGNED_AUTHORIZED_OBJECT_ID   => null,
            SignatureResource::SIGNED_UNAUTHORIZED_OBJECT_ID => null,
            SignatureResource::SIGNED_CORRELATION_ID         => null,
            SignatureResource::SIGNED_IP_ADDRESS             => null,
            SignatureResource::SIGNED_PROTOCOL               => $signedProtocol,
            SignatureResource::SIGNED_VERSION                => $userDelegationKey->signedVersion,
            SignatureResource::SIGNED_RESOURCE               => $userDelegationKey->signedService,
            SignatureResource::SIGNED_SNAPSHOT_TIME          => null,
            SignatureResource::SIGNED_ENCRYPTION_SCOPE       => null,
            SignatureResource::RESOURCE_CACHE_CONTROL        => null,
            SignatureResource::RESOURCE_CONTENT_DISPOSITION  => null,
            SignatureResource::RESOURCE_CONTENT_ENCODING     => null,
            SignatureResource::RESOURCE_CONTENT_LANGUAGE     => null,
            SignatureResource::RESOURCE_CONTENT_TYPE         => null,
        ];

        $stringToSign = implode("\n", $parameters);
        $signature    = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($userDelegationKey->value), true));

        unset($parameters[SignatureResource::SIGNED_CANONICAL_RESOURCE]);

        $queryParams                               = array_filter($parameters);
        $queryParams[SignatureResource::SIGNATURE] = $signature;

        return http_build_query($queryParams);
    }

    protected function getUserDelegationKey(DateTimeImmutable $expiry): UserDelegationKey
    {
        return (new AccountManager($this->request))->userDelegationKey(new KeyInfo([
            'Start'  => new DateTimeImmutable(),
            'Expiry' => $expiry,
        ]));
    }
}
