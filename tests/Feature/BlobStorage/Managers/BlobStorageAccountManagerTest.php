<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\{AccountInformation, GeoReplication, KeyInfo, UserDelegationKey};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Account\{PreflightBlobRequestManager, StoragePropertyManager};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\AccountManager;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'accounts');

it('should get account\'s managers', function (string $method, string $class) {
    $request = new RequestFake(new Config(['account' => 'account', 'key' => 'key']));

    expect(new AccountManager($request))
        ->{$method}()->toBeInstanceOf($class);
})->with([
    'Storage Properties'     => ['storageProperties', StoragePropertyManager::class],
    'Preflight Blob Request' => ['preflightBlobRequest', PreflightBlobRequestManager::class],
]);

it('should get account information', function () {
    $request = (new RequestFake(new Config(['account' => 'account', 'key' => 'key'])))
        ->withFakeResponse(new ResponseFake(headers: [
            'Server'              => ['Server'],
            'x-ms-request-id'     => ['d5a5d3f6-0000-0000-0000-000000000000'],
            'x-ms-version'        => ['2018-11-09'],
            'x-ms-sku-name'       => ['name'],
            'x-ms-account-kind'   => ['account-kind'],
            'x-ms-is-hns-enabled' => [true],
            'Date'                => ['2019-10-15T00:00:00'],
        ]));

    expect((new AccountManager($request))->information(['some' => 'value']))
        ->toBeInstanceOf(AccountInformation::class)
        ->server->toBe('Server')
        ->xMsRequestId->toBe('d5a5d3f6-0000-0000-0000-000000000000')
        ->xMsVersion->toBe('2018-11-09')
        ->xMsSkuName->toBe('name')
        ->xMsAccountKind->toBe('account-kind')
        ->xMsIsHnsEnabled->toBeTrue()
        ->date->format('Y-m-d\TH:i:s')->toBe('2019-10-15T00:00:00');

    $request->assertSentWithOptions(['some' => 'value'])
        ->assertGet('?comp=properties&restype=account');
});

it('should get account blob service stats', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <StorageServiceStats>
        <GeoReplication>
            <Status>live</Status>
            <LastSyncTime>2018-01-01T00:00:00.0000000Z</LastSyncTime>
        </GeoReplication>
    </StorageServiceStats>
    XML;

    $request = (new RequestFake(new Config(['account' => 'account', 'key' => 'key'])))
        ->withFakeResponse(new ResponseFake($body));

    expect((new AccountManager($request))->blobServiceStats(['some' => 'value']))
        ->toBeInstanceOf(GeoReplication::class)
        ->status->toBe('live')
        ->lastSyncTime->format('Y-m-d\TH:i:s')->toBe('2018-01-01T00:00:00');

    $request->assertUsingAccount('account-secondary')
        ->assertSentWithOptions(['some' => 'value'])
        ->assertGet('?comp=stats&restype=service');
});

it('should get account user delegation key', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <AccountInfo>
        <UserDelegationKey>
            <SignedOid>oid</SignedOid>
            <SignedTid>tid</SignedTid>
            <SignedStart>2020-01-01T00:00:00.0000000Z</SignedStart>
            <SignedExpiry>2020-01-02T00:00:00.0000000Z</SignedExpiry>
            <SignedService>service</SignedService>
            <SignedVersion>version</SignedVersion>
            <Value>value</Value>
        </UserDelegationKey>
    </AccountInfo>
    XML;

    $request = (new RequestFake(new Config(['account' => 'account', 'key' => 'key'])))
        ->withFakeResponse(new ResponseFake($body));

    $keyInfo = new KeyInfo([
        'Start'  => '2020-01-01T00:00:00.0000000Z',
        'Expiry' => '2020-01-02T00:00:00.0000000Z',
    ]);

    expect((new AccountManager($request))->userDelegationKey($keyInfo, ['some' => 'value']))
        ->toBeInstanceOf(UserDelegationKey::class)
        ->signedOid->toBe('oid')
        ->signedTid->toBe('tid')
        ->signedStart->format('Y-m-d\TH:i:s')->toBe('2020-01-01T00:00:00')
        ->signedExpiry->format('Y-m-d\TH:i:s')->toBe('2020-01-02T00:00:00')
        ->signedService->toBe('service')
        ->signedVersion->toBe('version')
        ->value->toBe('value');

    $request->assertPost('?comp=userdelegationkey&restype=service')
        ->assertSentWithOptions(['some' => 'value']);
});
