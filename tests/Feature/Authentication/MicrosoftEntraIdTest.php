<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\MicrosoftEntraId;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;
use Xray\AzureStoragePhpSdk\Fakes\Http\RequestFake;
use Xray\Tests\Fakes\ClientFake;

pest()->group('authentications');
covers(MicrosoftEntraId::class);

it('should implements Auth interface', function () {
    expect(MicrosoftEntraId::class)
        ->toImplement(Auth::class);
});

it('should fail if any required field is missing', function (string $field) {
    $config = [
        'account'     => 'account',
        'directory'   => 'directory',
        'application' => 'application',
        'secret'      => 'secret',
    ];

    unset($config[$field]);

    expect(fn () => new MicrosoftEntraId($config)) // @phpstan-ignore-line
        ->toThrow(RequiredFieldException::class, "Missing required parameters: {$field}");
})->with([
    'Missing Account'     => ['account'],
    'Missing Directory'   => ['directory'],
    'Missing Application' => ['application'],
    'Missing Secret'      => ['secret'],
]);

it('should get date formatted correctly', function () {
    $auth = new MicrosoftEntraId([
        'account'     => 'account',
        'directory'   => 'directory',
        'application' => 'application',
        'secret'      => 'secret',
    ]);

    expect($auth->getDate())
        ->toBe(gmdate('D, d M Y H:i:s T'));
});

it('should get the authentication account', function () {
    $auth = new MicrosoftEntraId([
        'account'     => 'account',
        'directory'   => 'directory',
        'application' => 'application',
        'secret'      => 'secret',
    ]);

    expect($auth->getAccount())
        ->toBe('account');
});

it('should get correctly the authentication signature from a login request', function () {
    /** @var string $body */
    $body = json_encode([
        'token_type'   => $tokeType = 'Bearer',
        'access_token' => $token    = 'token',
        'expires_in'   => 3600,
    ]);

    $client = (new ClientFake())
        ->withResponseFake($body);

    $auth = (new MicrosoftEntraId([
        'account'     => 'account',
        'directory'   => 'directory',
        'application' => $application = 'application',
        'secret'      => $secret      = 'secret',
    ]))->withRequestClient($client);

    expect($auth->getAuthentication(new RequestFake()))
        ->toBe("{$tokeType} {$token}");

    expect($auth->getAuthentication(new RequestFake()))
        ->toBe("{$tokeType} {$token}");

    $client->assertRequestSent(HttpVerb::POST->value, 'https://login.microsoftonline.com/directory/oauth2/v2.0/token', fn (array $options): bool => $options === [
        'form_params' => [
            'grant_type'    => 'client_credentials',
            'client_id'     => $application,
            'client_secret' => $secret,
            'scope'         => 'https://storage.azure.com/.default',
        ],
    ]);
});
