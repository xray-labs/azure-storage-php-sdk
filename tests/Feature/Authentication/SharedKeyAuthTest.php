<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\Contracts\Auth;
use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Http\Headers;

uses()->group('authentications');

it('should implements Auth interface', function () {
    expect(SharedKeyAuth::class)
        ->toImplement(Auth::class);
});

it('should get date formatted correctly', function () {
    $auth = new SharedKeyAuth(new Config([
        'account' => 'account',
        'key'     => base64_encode('key'),
    ]));

    expect($auth->getDate())
        ->toBe(gmdate('D, d M Y H:i:s T'));
});

it('should get correctly the authentication signature for all http methods', function (HttpVerb $verb) {
    $decodedKey = 'my-decoded-account-key';

    $auth = new SharedKeyAuth(new Config([
        'account' => $account = 'account',
        'key'     => base64_encode($decodedKey),
    ]));

    $headers      = new Headers();
    $stringToSign = "{$verb->value}\n{$headers->toString()}\n\n/{$account}/";

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, $decodedKey, true));

    expect($auth->getAuthentication($verb, $headers, '/'))
        ->toBe("SharedKey {$account}:{$signature}");
})->with([
    'GET'     => [HttpVerb::GET],
    'PUT'     => [HttpVerb::PUT],
    'POST'    => [HttpVerb::POST],
    'DELETE'  => [HttpVerb::DELETE],
    'OPTIONS' => [HttpVerb::OPTIONS],
    'HEAD'    => [HttpVerb::HEAD],
    'PATCH'   => [HttpVerb::PATCH],
]);

it('should get correctly the authentication signature for all headers', function (string $headerMethod, int|string $headerValue) {
    $decodedKey = 'my-decoded-account-key';

    $auth = new SharedKeyAuth(new Config([
        'account' => $account = 'account',
        'key'     => base64_encode($decodedKey),
    ]));

    $verb = HttpVerb::GET;

    $headers      = (new Headers())->{$headerMethod}($headerValue);
    $stringToSign = "{$verb->value}\n{$headers->toString()}\n\n/{$account}/";

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, $decodedKey, true));

    expect($auth->getAuthentication($verb, $headers, '/'))
        ->toBe("SharedKey {$account}:{$signature}");
})->with([
    'Content Encoding'    => ['setContentEncoding', 'utf-8'],
    'Content Language'    => ['setContentLanguage', 'en-US'],
    'Content Length'      => ['setContentLength', 100],
    'Content MD5'         => ['setContentMD5', 'content-md5'],
    'Content Type'        => ['setContentType', 'application/xml'],
    'Date'                => ['setDate', '2024-01-01T00:00:00.000Z'],
    'If Modified Since'   => ['setIfModifiedSince', '2019-01-01T00:00:00.000Z'],
    'If Match'            => ['setIfMatch', 'if-match'],
    'If None Match'       => ['setIfNoneMatch', 'if-none-match'],
    'If Unmodified Since' => ['setIfUnmodifiedSince', '2019-01-01T00:00:00.000Z'],
    'Range'               => ['setRange', 'bytes=0-1000'],
]);

it('should get correctly the authentication signature for all canonical headers', function (string $headerMethod, string $headerValue) {
    $decodedKey = 'my-decoded-account-key';

    $auth = new SharedKeyAuth(new Config([
        'account' => $account = 'account',
        'key'     => base64_encode($decodedKey),
    ]));

    $verb = HttpVerb::GET;

    $headers      = (new Headers())->withAdditionalHeaders([$headerMethod => $headerValue]);
    $stringToSign = "{$verb->value}\n{$headers->toString()}\n{$headers->getCanonicalHeaders()}\n/{$account}/";

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, $decodedKey, true));

    expect($auth->getAuthentication($verb, $headers, '/'))
        ->toBe("SharedKey {$account}:{$signature}");
})->with([
    'Auth Date'          => [Resource::AUTH_DATE_KEY, '2024-06-10T00:00:00.000Z'],
    'Auth Version'       => [Resource::AUTH_VERSION_KEY, '2021-06-08'],
    'Client Request Id'  => [Resource::CLIENT_REQUEST_ID_KEY, 'client-request-id'],
    'Request Id'         => [Resource::REQUEST_ID_KEY, 'request-id'],
    'Lease Id'           => [Resource::LEASE_ID_KEY, 'lease-id'],
    'Lease Action'       => [Resource::LEASE_ACTION_KEY, 'renew'],
    'Lease Break Period' => [Resource::LEASE_BREAK_PERIOD_KEY, 'break-period'],
    'Lease Duration'     => [Resource::LEASE_DURATION_KEY, 'duration'],
    'Lease Proposed Id'  => [Resource::LEASE_PROPOSED_ID_KEY, 'proposed-id'],
]);
