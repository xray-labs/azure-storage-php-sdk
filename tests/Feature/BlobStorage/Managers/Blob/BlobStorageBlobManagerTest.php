<?php

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, Blobs, File};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\BlobType;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\{BlobManager, BlobMetadataManager, BlobPageManager, BlobPropertyManager, BlobTagManager};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Http\Response as BaseResponse;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'blobs');

it('should get the blob\'s managers', function (string $method, string $class) {
    $request = new RequestFake(new Config(['account' => 'account', 'key' => 'key']));

    expect((new BlobManager($request, 'container'))->{$method}('blob'))
        ->toBeInstanceOf($class);
})->with([
    'Properties' => ['properties', BlobPropertyManager::class],
    'Metadata'   => ['metadata', BlobMetadataManager::class],
    'Tags'       => ['tags', BlobTagManager::class],
]);

it('should get blob pages manager', function () {
    $request = new RequestFake(new Config(['account' => 'account', 'key' => 'key']));

    expect((new BlobManager($request, 'container'))->pages())
        ->toBeInstanceOf(BlobPageManager::class);
});

it('should create a new blob block', function () {
    $request = (new RequestFake(new Config(['account' => 'account', 'key' => 'key'])))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $file = new File('name', 'content');

    expect((new BlobManager($request, $container = 'container'))->putBlock($file, ['option' => 'value']))
        ->toBeTrue();

    $request->assertPut("{$container}/{$file->name}?resttype=blob")
        ->assertSentWithOptions(['option' => 'value'])
        ->assertSentWithHeaders([
            Resource::BLOB_TYPE         => BlobType::BLOCK->value,
            Resource::BLOB_CONTENT_MD5  => $file->contentMD5,
            Resource::BLOB_CONTENT_TYPE => $file->contentType,
            Resource::CONTENT_MD5       => $file->contentMD5,
            Resource::CONTENT_TYPE      => $file->contentType,
            Resource::CONTENT_LENGTH    => $file->contentLength,
        ]);
});

it('should get a blob', function () {
    $request = (new RequestFake(new Config(['account' => 'account', 'key' => 'key'])))
        ->withFakeResponse(new ResponseFake($body = 'blob content', headers: [
            'Content-Length'        => ['10'],
            'Content-Type'          => ['plain/text'],
            'Content-MD5'           => ['Q2hlY2sgSW50ZWdyaXR5'],
            'Last-Modified'         => ['2021-01-01T00:00:00.0000000Z'],
            'Accept-Ranges'         => ['bytes'],
            'ETag'                  => ['"0x8D8D8D8D8D8D8D9"'],
            'Vary'                  => ['Accept-Encoding'],
            'Server'                => ['Windows-Azure-Blob/1.0 Microsoft-HTTPAPI/2.0'],
            'x-ms-request-id'       => ['0'],
            'x-ms-version'          => ['2019-02-02'],
            'x-ms-creation-time'    => ['2020-01-01T00:00:00.0000000Z'],
            'x-ms-lease-status'     => ['unlocked'],
            'x-ms-lease-state'      => ['available'],
            'x-ms-blob-type'        => ['BlockBlob'],
            'x-ms-server-encrypted' => ['true'],
            'Date'                  => ['2015-10-21T07:28:00.0000000Z'],
        ]));

    expect((new BlobManager($request, $container = 'container'))->get($blob = 'blob.text', ['option' => 'value']))
        ->toBeInstanceOf(File::class)
        ->name->toBe($blob)
        ->content->toBe($body)
        ->contentLength->toBe(10)
        ->contentType->toBe('plain/text')
        ->contentMD5->toBe('Q2hlY2sgSW50ZWdyaXR5')
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->acceptRanges->toBe('bytes')
        ->eTag->toBe('"0x8D8D8D8D8D8D8D9"')
        ->vary->toBe('Accept-Encoding')
        ->server->toBe('Windows-Azure-Blob/1.0 Microsoft-HTTPAPI/2.0')
        ->xMsRequestId->toBe('0')
        ->xMsVersion->format('Y-m-d')->toBe('2019-02-02')
        ->xMsCreationTime->format('Y-m-d\TH:i:s')->toBe('2020-01-01T00:00:00')
        ->xMsLeaseStatus->toBe('unlocked')
        ->xMsLeaseState->toBe('available')
        ->xMsBlobType->toBe('BlockBlob')
        ->xMsServerEncrypted->toBe(true);

    $request->assertGet("{$container}/{$blob}?resttype=blob")
        ->assertSentWithOptions(['option' => 'value']);
});

it('should list all blobs', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <EnumerationResults>
        <Blobs>
            <Blob>
                <Name>name</Name>
                <Snapshot>2021-01-01T00:00:00.0000000Z</Snapshot>
                <Version>2021-01-01T00:00:00.0000000Z</Version>
                <IsCurrentVersion>true</IsCurrentVersion>
                <Properties>
                    <Last-Modified>2021-01-01T00:00:00.0000000Z</Last-Modified>
                    <Content-Length>10</Content-Length>
                    <Content-Type>plain/text</Content-Type>
                    <Content-MD5>Q2hlY2sgSW50ZWdyaXR5</Content-MD5>
                    <Etag>0x8D8D8D8D8D8D8D9</Etag>
                    <LeaseStatus>unlocked</LeaseStatus>
                    <LeaseState>available</LeaseState>
                    <ServerEncrypted>true</ServerEncrypted>
                </Properties>
                <Deleted>false</Deleted>
            </Blob>
        </Blobs>
    </EnumerationResults>
    XML;

    $request = (new RequestFake(new Config(['account' => 'account', 'key' => 'key'])))
        ->withFakeResponse(new ResponseFake($body));

    $result = (new BlobManager($request, $container = 'container'))->list(['option' => 'value']);

    expect($result)
        ->toBeInstanceOf(Blobs::class)
        ->toHaveCount(1)
        ->and($result->first())
        ->toBeInstanceOf(Blob::class)
        ->name->toBe('name')
        ->snapshot->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->versionId->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->isCurrentVersion->toBeTrue()
        ->and($result->first()->properties)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->contentLength->toBe('10')
        ->contentType->toBe('plain/text')
        ->contentMD5->toBe('Q2hlY2sgSW50ZWdyaXR5')
        ->eTag->toBe('0x8D8D8D8D8D8D8D9')
        ->leaseStatus->toBe('unlocked')
        ->leaseState->toBe('available')
        ->serverEncrypted->toBe(true);

    $request->assertGet("{$container}/?restype=container&comp=list")
        ->assertSentWithOptions(['option' => 'value']);
});
