<?php

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel\ContainerAccessLevels;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\{Container, ContainerMetadata, ContainerProperties};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerLeaseManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequiredFieldException;
use Sjpereira\AzureStoragePhpSdk\Http\Response as BaseResponse;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'entities', 'containers');

it('should throw an exception if the container\'s name isn\'t provided', function () {
    $container = new Container([
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]);

    expect($container)->toBeInstanceOf(Container::class);
})->throws(RequiredFieldException::class);

it('should call manager\'s list access level', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <EnumerationResults>
        <SignedIdentifier>
            <Id>id</Id>
            <AccessPolicy>
                <Start>2024-06-10T00:00:00.0000000Z</Start>
                <Expiry>2025-06-10T00:00:00.0000000Z</Expiry>
                <Permission>permission</Permission>
            </AccessPolicy>
        </SignedIdentifier>
    </EnumerationResults>
    XML;

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake($body));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->listAccessLevels())
        ->toBeInstanceOf(ContainerAccessLevels::class);
});

it('should get the container\'s properties', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'                                  => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'                                           => ['etag'],
            'Server'                                         => ['server'],
            'x-ms-request-id'                                => ['request-id'],
            'x-ms-version'                                   => ['version'],
            'x-ms-lease-status'                              => ['lease-status'],
            'x-ms-lease-state'                               => ['lease-state'],
            'x-ms-has-immutability-policy'                   => ['true'],
            'x-ms-has-legal-hold'                            => ['true'],
            'x-ms-immutable-storage-with-versioning-enabled' => ['true'],
            'x-ms-default-encryption-scope'                  => ['default-encryption-scope'],
            'x-ms-deny-encryption-scope-override'            => ['true'],
            'Date'                                           => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->properties())
        ->toBeInstanceOf(ContainerProperties::class);
});

it('should get the container\'s metadata', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'   => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'            => ['etag'],
            'Server'          => ['server'],
            'x-ms-request-id' => ['request-id'],
            'x-ms-version'    => ['version'],
            'Date'            => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->metadata())
        ->toBeInstanceOf(ContainerMetadata::class);
});

it('should delete the container', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_ACCEPTED));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->delete())->toBeTrue();
});

it('should restore the deleted container', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->restore())->toBeTrue();
});

it('should lease the container', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->lease())->toBeInstanceOf(ContainerLeaseManager::class);
});

it('should get the blobs from the container', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))));

    $manager = new ContainerManager($request);

    $container = (new Container([
        'Name'       => 'name',
        'Deleted'    => false,
        'Version'    => 'version',
        'Properties' => [],
    ]))->setManager($manager);

    expect($container->blobs())->toBeInstanceOf(BlobManager::class);
});
