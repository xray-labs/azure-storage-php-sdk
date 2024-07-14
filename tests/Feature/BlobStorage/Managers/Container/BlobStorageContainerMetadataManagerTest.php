<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerMetadata;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerMetadataManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'containers');

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

    expect((new ContainerMetadataManager($request))->get($container = 'container', ['option' => 'value']))
        ->toBeInstanceOf(ContainerMetadata::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2024-06-10T00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->xMsRequestId->toBe('request-id')
        ->xMsVersion->toBe('version')
        ->date->format('Y-m-d\TH:i:s')->toBe('2024-06-11T00:00:00');

    $request->assertGet("{$container}?comp=metadata&restype=container")
        ->assertSentWithOptions(['option' => 'value']);
});

it('should throw an exception if the metadata key is invalid', function (string $key, string $message) {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

    expect(fn () => (new ContainerMetadataManager($request))->save('container', [
        'valid' => 'valid',
        $key    => 'invalid',
    ]))->toThrow(InvalidArgumentException::class, "Invalid metadata key: {$key}. {$message}");
})->with([
    'Starts With Number' => ['0test', 'Metadata keys cannot start with a number.'],
    'Invalid Characters' => ['test*', 'Only alphanumeric characters and underscores are allowed.'],
]);

it('should save the container\'s metadata', function () {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

    expect((new ContainerMetadataManager($request))->save($container = 'container', [
        'test'    => 'test',
        'test_02' => 'test_02',
    ]))->toBeTrue();

    $request->assertPut("{$container}?restype=container&comp=metadata")
        ->withHeaders([
            Resource::METADATA_PREFIX . 'test'    => urlencode('test'),
            Resource::METADATA_PREFIX . 'test_02' => urlencode('test_02'),
        ]);
});