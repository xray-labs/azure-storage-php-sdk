<?php

use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, Blobs, File, Properties};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\{BlobType, ExpirationOption};
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\{BlobLeaseManager, BlobManager, BlobMetadataManager, BlobPageManager, BlobPropertyManager, BlobTagManager};
use Xray\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Xray\AzureStoragePhpSdk\Http\Response as BaseResponse;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'blobs');

it('should get the blob\'s managers', function (string $method, string $class) {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

    expect((new BlobManager($request, 'container'))->{$method}('blob'))
        ->toBeInstanceOf($class); // @phpstan-ignore-line
})->with([
    'Properties' => ['properties', BlobPropertyManager::class],
    'Metadata'   => ['metadata', BlobMetadataManager::class],
    'Tags'       => ['tags', BlobTagManager::class],
]);

it('should get blob pages manager', function () {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

    expect((new BlobManager($request, 'container'))->pages())
        ->toBeInstanceOf(BlobPageManager::class);
});

it('should get blob lease manager', function () {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

    expect((new BlobManager($request, 'container'))->lease('blob'))
        ->toBeInstanceOf(BlobLeaseManager::class);
});

it('should create a new blob block', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
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
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
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

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake($body));

    $result = (new BlobManager($request, $container = 'container'))->list(['option' => 'value'], includes: ['metadata', 'snapshots']);

    expect($result)
        ->toBeInstanceOf(Blobs::class)
        ->toHaveCount(1)
        ->and($result->first())
        ->toBeInstanceOf(Blob::class)
        ->name->toBe('name')
        ->snapshot->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->versionId->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->isCurrentVersion->toBeTrue()
        ->and($result->first()?->properties)
        ->toBeInstanceOf(Properties::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->contentLength->toBe('10')
        ->contentType->toBe('plain/text')
        ->contentMD5->toBe('Q2hlY2sgSW50ZWdyaXR5')
        ->eTag->toBe('0x8D8D8D8D8D8D8D9')
        ->leaseStatus->toBe('unlocked')
        ->leaseState->toBe('available')
        ->serverEncrypted->toBe(true);

    $request->assertGet("{$container}/?restype=container&comp=list&include=metadata,snapshots")
        ->assertSentWithOptions(['option' => 'value']);
});

it('should an exception if the BlobIncludeOption is invalid', function () {
    $blobManager = new BlobManager(new RequestFake(new Config(new SharedKeyAuth('account', 'key'))), 'container');

    $blobManager->list(includes: ['invalid']);
})->throws(InvalidArgumentException::class);

it('should find by tag', function () {
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

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake($body));

    $result = (new BlobManager($request, $container = 'container'))
        ->findByTag(['option' => 'value'])
        ->where('key', 'value')
        ->build();

    expect($result)
        ->toBeInstanceOf(Blobs::class)
        ->toHaveCount(1)
        ->and($result->first())
        ->toBeInstanceOf(Blob::class)
        ->name->toBe('name')
        ->snapshot->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->versionId->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->isCurrentVersion->toBeTrue()
        ->and($result->first()?->properties)
        ->toBeInstanceOf(Properties::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->contentLength->toBe('10')
        ->contentType->toBe('plain/text')
        ->contentMD5->toBe('Q2hlY2sgSW50ZWdyaXR5')
        ->eTag->toBe('0x8D8D8D8D8D8D8D9')
        ->leaseStatus->toBe('unlocked')
        ->leaseState->toBe('available')
        ->serverEncrypted->toBe(true);

    $request->assertGet("{$container}/?restype=container&comp=blobs&where=%22key%22%3D%27value%27")
        ->assertSentWithOptions(['option' => 'value']);
});

it('should set expiry', function () {
    $expiryTime       = new DateTime('+1 hour');
    $expirationOption = ExpirationOption::ABSOLUTE;

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))));

    $result = (new BlobManager($request, 'container'))
        ->setExpiry('test', $expirationOption, $expiryTime, ['option' => 'value']);

    $request->assertSentWithHeaders([
        Resource::EXPIRY_OPTION => $expirationOption->value,
        Resource::EXPIRY_TIME   => $expiryTime->format('D, d M Y H:i:s \G\M\T'),
    ])
        ->assertSentWithOptions(['option' => 'value'])
        ->assertPut('container/test?resttype=blob&comp=expiry');

    expect($result)
        ->toBeTrue();
});

it('should set expiration as never expire', function () {
    $expirationOption = ExpirationOption::NEVER_EXPIRE;

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))));

    $result = (new BlobManager($request, 'container'))
        ->setExpiry('test', $expirationOption, options: ['option' => 'value']);

    $request->assertSentWithHeaders([
        Resource::EXPIRY_OPTION => $expirationOption->value,
    ])
        ->assertSentWithOptions(['option' => 'value'])
        ->assertPut('container/test?resttype=blob&comp=expiry');

    expect($result)
        ->toBeTrue();
});

it('should delete a blob', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_ACCEPTED));

    $snapshot = new DateTimeImmutable('2021-01-01T00:00:00.0000000Z');

    $result = (new BlobManager($request, $container = 'container'))->delete($blob = 'blob', snapshot: $snapshot, force: true);

    expect($result)
        ->toBeTrue();

    $url = "{$container}/{$blob}?" . http_build_query([
        'resttype'              => 'blob',
        'snapshot'              => '2021-01-01T00:00:00.0000000Z',
        'x-ms-delete-snapshots' => 'include',
    ]);

    $request->assertDelete($url);
});

it('should restore a blob', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))));

    $result = (new BlobManager($request, $container = 'container'))
        ->restore($blob = 'blob');

    expect($result)
        ->toBeTrue();

    $request->assertPut("{$container}/{$blob}?comp=undelete&resttype=blob");
});

it('should create a snapshot', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $result = (new BlobManager($request, $container = 'container'))
        ->createSnapshot($blob = 'blob');

    expect($result)
        ->toBeTrue();

    $request->assertPut("{$container}/{$blob}?comp=snapshot&resttype=blob");
});

it('should copy a blob', function () {
    $snapshot = new DateTimeImmutable('2024-07-14T15:02:29.8018334Z');

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_ACCEPTED));

    $result = (new BlobManager($request, $container = 'container'))
        ->copy($source = 'source', $destination = 'destination', ['option' => 'value'], $snapshot);

    expect($result)
        ->toBeTrue();

    $request->assertPut("{$container}/{$destination}?resttype=blob")
        ->assertSentWithOptions(['option' => 'value'])
        ->assertSentWithHeaders([
            Resource::COPY_SOURCE => "http://account.microsoft.azure/{$container}/{$source}?snapshot=2024-07-14T15%3A02%3A29.8018330Z",
        ]);
});
