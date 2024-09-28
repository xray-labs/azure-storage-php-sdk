<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\SharedAccessSignature\UserDelegationSas;
use Xray\AzureStoragePhpSdk\Authentication\{MicrosoftEntraId, SharedKeyAuth};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\AccessTokenPermission;
use Xray\AzureStoragePhpSdk\BlobStorage\SignatureResource;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\SharedAccessSignature;
use Xray\AzureStoragePhpSdk\Exceptions\Authentication\InvalidAuthenticationMethodException;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidResourceTypeException;
use Xray\AzureStoragePhpSdk\Fakes\Http\{RequestFake, ResponseFake};

use function Xray\AzureStoragePhpSdk\Support\convert_to_ISO;

pest()->group('authentications', 'shared-access-signatures');
covers(UserDelegationSas::class);

it('should implements SharedAccessSignature interface', function () {
    expect(UserDelegationSas::class)
        ->toImplement(SharedAccessSignature::class);
});

it('should throw an exception if the authentication method is not supported', function () {
    $request = new RequestFake(new SharedKeyAuth(['account' => 'account', 'key' => 'key']));

    (new UserDelegationSas($request))
        ->buildTokenUrl(AccessTokenPermission::READ, new DateTimeImmutable());
})->throws(InvalidAuthenticationMethodException::class, sprintf('Invalid Authentication Method. [%s] needed, but [%s] given.', MicrosoftEntraId::class, SharedKeyAuth::class));

it('should throw an exception if the signed service is not supported', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <UserDelegationKey>
        <SignedOid>oid</SignedOid>
        <SignedTid>tid</SignedTid>
        <SignedStart>2020-10-10T00:00:00Z</SignedStart>
        <SignedExpiry>2020-10-11T00:00:00Z</SignedExpiry>
        <SignedService>invalid</SignedService>
        <SignedVersion>version</SignedVersion>
        <Value>value</Value>
    </UserDelegationKey>
    XML;

    $request = (new RequestFake(new MicrosoftEntraId([
        'account'     => 'account',
        'directory'   => 'directory',
        'application' => 'application',
        'secret'      => 'secret',
    ])))->withFakeResponse(new ResponseFake($body));

    (new UserDelegationSas($request))
        ->buildTokenUrl(AccessTokenPermission::READ, new DateTimeImmutable());

    $request->assertPost('?comp=userdelegationkey&restype=service');
})->throws(InvalidResourceTypeException::class, 'The [invalid] signed service is not valid. The allowed services are [b, c, f, s, d].');

it('should build the query param token correctly', function () {
    $signedKeyObjectId = '050b9fa2-5df9-47b2-95d0-3342be8d943c';
    $signedKeyTenantId = '0a20a1a3-567e-45a2-8c3a-3300a41c8770';
    $signedStart       = '2024-08-01T00:00:00Z';
    $signedExpiry      = '2024-08-03T00:00:00Z';
    $signedService     = 'b';
    $signedVersion     = '2024-05-04';
    $value             = 'cbUl8Ca1gwjmcFp+PTRaa5lIDJ3INnFS0suuPSCT2VA=';

    $body = <<<XML
    <?xml version="1.0"?>
    <UserDelegationKey>
        <SignedOid>{$signedKeyObjectId}</SignedOid>
        <SignedTid>{$signedKeyTenantId}</SignedTid>
        <SignedStart>{$signedStart}</SignedStart>
        <SignedExpiry>{$signedExpiry}</SignedExpiry>
        <SignedService>{$signedService}</SignedService>
        <SignedVersion>{$signedVersion}</SignedVersion>
        <Value>{$value}</Value>
    </UserDelegationKey>
    XML;

    $signedStart  = new DateTimeImmutable($signedStart);
    $signedExpiry = new DateTimeImmutable($signedExpiry);
    $container    = 'container';
    $blob         = 'blob.txt';

    $request = (new RequestFake(new MicrosoftEntraId([
        'account'     => $account = 'account',
        'directory'   => 'directory',
        'application' => 'application',
        'secret'      => 'secret',
    ])))
        ->withFakeResponse(new ResponseFake($body))
        ->withResource("/{$container}/{$blob}");

    $expectedToken = createSignatureTokenForUserDelegationSasTest([
        'account'    => $account,
        'container'  => $container,
        'blob'       => $blob,
        'permission' => ($permission = AccessTokenPermission::READ)->value,
        'start'      => $signedStart,
        'expiry'     => $signedExpiry,
        'service'    => $signedService,
        'version'    => $signedVersion,
        'oid'        => $signedKeyObjectId,
        'tid'        => $signedKeyTenantId,
        'key'        => $value,
    ]);

    $token = (new UserDelegationSas($request))
        ->buildTokenUrl($permission, $signedExpiry);

    expect($token)->toBe($expectedToken);

    $request->assertPost('?comp=userdelegationkey&restype=service');
});

/** @param array<string, string|DateTimeImmutable> $arguments */
function createSignatureTokenForUserDelegationSasTest(array $arguments): string
{
    /** @var string $account */
    $account = $arguments['account'];

    /** @var string $container */
    $container = $arguments['container'];

    /** @var string $blob */
    $blob = $arguments['blob'];

    $signedResource = "/blob/{$account}/{$container}/{$blob}";

    /** @var array<string> $parameters */
    $parameters = [
        SignatureResource::SIGNED_PERMISSION             => $arguments['permission'],
        SignatureResource::SIGNED_START                  => convert_to_ISO($arguments['start']),
        SignatureResource::SIGNED_EXPIRY                 => convert_to_ISO($arguments['expiry']),
        SignatureResource::SIGNED_CANONICAL_RESOURCE     => $signedResource,
        SignatureResource::SIGNED_OBJECT_ID              => $arguments['oid'],
        SignatureResource::SIGNED_TENANT_ID              => $arguments['tid'],
        SignatureResource::SIGNED_KEY_START_TIME         => convert_to_ISO($arguments['start']),
        SignatureResource::SIGNED_KEY_EXPIRY_TIME        => convert_to_ISO($arguments['expiry']),
        SignatureResource::SIGNED_KEY_SERVICE            => $arguments['service'],
        SignatureResource::SIGNED_KEY_VERSION            => $arguments['version'],
        SignatureResource::SIGNED_AUTHORIZED_OBJECT_ID   => null,
        SignatureResource::SIGNED_UNAUTHORIZED_OBJECT_ID => null,
        SignatureResource::SIGNED_CORRELATION_ID         => null,
        SignatureResource::SIGNED_IP_ADDRESS             => null,
        SignatureResource::SIGNED_PROTOCOL               => 'http',
        SignatureResource::SIGNED_VERSION                => $arguments['version'],
        SignatureResource::SIGNED_RESOURCE               => $arguments['service'],
        SignatureResource::SIGNED_SNAPSHOT_TIME          => null,
        SignatureResource::SIGNED_ENCRYPTION_SCOPE       => null,
        SignatureResource::RESOURCE_CACHE_CONTROL        => null,
        SignatureResource::RESOURCE_CONTENT_DISPOSITION  => null,
        SignatureResource::RESOURCE_CONTENT_ENCODING     => null,
        SignatureResource::RESOURCE_CONTENT_LANGUAGE     => null,
        SignatureResource::RESOURCE_CONTENT_TYPE         => null,
    ];

    $stringToSign = implode("\n", $parameters);

    /** @var string $key */
    $key = $arguments['key'];

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, base64_decode($key), true));

    unset($parameters[SignatureResource::SIGNED_CANONICAL_RESOURCE]);

    $queryParams                               = array_filter($parameters);
    $queryParams[SignatureResource::SIGNATURE] = $signature;

    return http_build_query($queryParams);
}
