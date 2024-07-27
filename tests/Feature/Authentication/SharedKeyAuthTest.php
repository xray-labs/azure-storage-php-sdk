<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\BlobStorage\{Resource};
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Http\Headers;
use Xray\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('authentications');

it('should implements Auth interface', function () {
    expect(SharedKeyAuth::class)
        ->toImplement(Auth::class);
});

it('should get date formatted correctly', function () {
    $auth = new SharedKeyAuth('account', 'key');

    expect($auth->getDate())
        ->toBe(gmdate('D, d M Y H:i:s T'));
});

it('should get the authentication account', function () {
    $auth = new SharedKeyAuth('account', 'key');

    expect($auth->getAccount())
        ->toBe('account');
});

it('should get correctly the authentication signature for all http methods', function (HttpVerb $verb) {
    $decodedKey = 'my-decoded-account-key';

    $auth = new SharedKeyAuth($account = 'account', base64_encode($decodedKey));

    $request = (new RequestFake())
        ->withVerb($verb);

    $stringToSign = "{$request->getVerb()->value}\n{$request->getHttpHeaders()->toString()}\n\n/{$account}/";

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, $decodedKey, true));

    expect($auth->getAuthentication($request))
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

    $auth = new SharedKeyAuth($account = 'account', base64_encode($decodedKey));

    $request = (new RequestFake())
        ->withVerb(HttpVerb::GET)
        ->withHttpHeaders((new Headers())->{$headerMethod}($headerValue));

    $stringToSign = "{$request->getVerb()->value}\n{$request->getHttpHeaders()->toString()}\n\n/{$account}/";

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, $decodedKey, true));

    expect($auth->getAuthentication($request))
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

    $auth = new SharedKeyAuth($account = 'account', base64_encode($decodedKey));

    $request = (new RequestFake())
        ->withVerb(HttpVerb::GET)
        ->withHttpHeaders((new Headers())->withAdditionalHeaders([$headerMethod => $headerValue]));

    $stringToSign = "{$request->getVerb()->value}\n{$request->getHttpHeaders()->toString()}\n{$request->getHttpHeaders()->getCanonicalHeaders()}\n/{$account}/";

    $signature = base64_encode(hash_hmac('sha256', $stringToSign, $decodedKey, true));

    expect($auth->getAuthentication($request))
        ->toBe("SharedKey {$account}:{$signature}");
})->with([
    'Auth Date'          => [Resource::AUTH_DATE, '2024-06-10T00:00:00.000Z'],
    'Auth Version'       => [Resource::AUTH_VERSION, '2021-06-08'],
    'Client Request Id'  => [Resource::CLIENT_REQUEST_ID, 'client-request-id'],
    'Request Id'         => [Resource::REQUEST_ID, 'request-id'],
    'Lease Id'           => [Resource::LEASE_ID, 'lease-id'],
    'Lease Action'       => [Resource::LEASE_ACTION, 'renew'],
    'Lease Break Period' => [Resource::LEASE_BREAK_PERIOD, 'break-period'],
    'Lease Duration'     => [Resource::LEASE_DURATION, 'duration'],
    'Lease Proposed Id'  => [Resource::LEASE_PROPOSED_ID, 'proposed-id'],
]);
