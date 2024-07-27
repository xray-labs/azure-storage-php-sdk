<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobMetadata;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobMetadataManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'blobs');

it('should get the blob\'s metadata', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Content-Length'                      => 1024,
            'Last-Modified'                       => '2021-01-01T00:00:00.0000000Z',
            'ETag'                                => '0x8D8D8D8D8D8D8D9',
            'Vary'                                => '*',
            'Server'                              => 'server',
            'x-ms-request-id'                     => '0',
            'x-ms-version'                        => '2019-02-02',
            Resource::METADATA_PREFIX . 'test'    => 'valid',
            Resource::METADATA_PREFIX . 'test_02' => 'valid-02',
        ]));

    $result = (new BlobMetadataManager($request, $container = 'container', $blob = 'blob.txt'))
        ->get(['option' => 'value']);

    expect($result)
        ->toBeInstanceOf(BlobMetadata::class)
        ->contentLength->toBe(1024)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->eTag->toBe('0x8D8D8D8D8D8D8D9')
        ->vary->toBe('*')
        ->server->toBe('server')
        ->xMsRequestId->toBe('0')
        ->xMsVersion->toBe('2019-02-02')
        ->and($result->metadata)
        ->toBeArray()
        ->toHaveCount(2)
        ->toBe([
            Resource::METADATA_PREFIX . 'test'    => 'valid',
            Resource::METADATA_PREFIX . 'test_02' => 'valid-02',
        ]);

    $request->assertGet("{$container}/{$blob}?comp=metadata&resttype=blob")
        ->assertSentWithOptions(['option' => 'value']);
});

it('should save the blob\'s metadata', function () {
    $request = new RequestFake();

    $blobMetadata = new BlobMetadata([
        Resource::METADATA_PREFIX . 'test'    => 'valid',
        Resource::METADATA_PREFIX . 'test_02' => 'valid-02',
    ]);

    $manager = new BlobMetadataManager($request, $container = 'container', $blob = 'blob.txt');

    expect($manager->save($blobMetadata, ['option' => 'value']))->toBeTrue();

    $request->assertPut("{$container}/{$blob}?comp=metadata&resttype=blob")
        ->assertSentWithOptions(['option' => 'value'])
        ->assertSentWithHeaders([
            Resource::METADATA_PREFIX . 'test'    => urlencode('valid'),
            Resource::METADATA_PREFIX . 'test_02' => urlencode('valid-02'),
        ]);
});
