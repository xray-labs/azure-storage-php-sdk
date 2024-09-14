<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobProperty;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobPropertyManager;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

pest()->group('blob-storage', 'managers', 'blobs');
covers(BlobPropertyManager::class);

it('should get the blob\'s properties', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'                  => '2021-01-01T00:00:00.0000000Z',
            'ETag'                           => '0x8D8D8D8D8D8D8D9',
            'Content-Length'                 => '0',
            'Content-Type'                   => 'plain/text',
            'Content-MD5'                    => 'Q2hlY2sgSW50ZWdyaXR5',
            'Content-Encoding'               => 'gzip',
            'Content-Language'               => 'en-US',
            'x-ms-creation-time'             => '2021-01-01T00:00:00.0000000Z',
            'x-ms-tag-count'                 => '0',
            'x-ms-blob-type'                 => 'BlockBlob',
            'x-ms-copy-completion-time'      => '2021-01-01T00:00:00.0000000Z',
            'x-ms-copy-status-description'   => 'copy status description',
            'x-ms-copy-id'                   => 'copy id',
            'x-ms-copy-progress'             => '0',
            'x-ms-copy-source'               => 'copy source',
            'x-ms-copy-status'               => 'success',
            'x-ms-incremental-copy'          => 'false',
            'x-ms-copy-destination-snapshot' => '2021-01-01T00:00:00.0000000Z',
            'x-ms-lease-duration'            => '0',
            'x-ms-lease-state'               => 'available',
            'x-ms-lease-status'              => 'unlocked',
        ]));

    azure_app()->instance(Request::class, $request);

    $manager = new BlobPropertyManager($request, $container = 'container', $blob = 'blob.txt');

    expect($manager->get(['option' => 'value']))
        ->toBeInstanceOf(BlobProperty::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->eTag->toBe('0x8D8D8D8D8D8D8D9')
        ->contentLength->toBe(0)
        ->contentType->toBe('plain/text')
        ->contentMD5->toBe('Q2hlY2sgSW50ZWdyaXR5')
        ->contentEncoding->toBe('gzip')
        ->contentLanguage->toBe('en-US')
        ->creationTime->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->tagCount->toBe(0)
        ->blobType->toBe('BlockBlob')
        ->copyCompletionTime->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->copyStatusDescription->toBe('copy status description')
        ->copyId->toBe('copy id')
        ->copyProgress->toBe(0)
        ->copySource->toBe('copy source')
        ->copyStatus->toBe('success')
        ->incrementalCopy->toBe(false)
        ->copyDestinationSnapshot->format('Y-m-d\TH:i:s')->toBe('2021-01-01T00:00:00')
        ->leaseDuration->toBe('0')
        ->leaseState->toBe('available')
        ->leaseStatus->toBe('unlocked');

    $request->assertGet("{$container}/{$blob}?resttype=blob")
        ->assertSentWithOptions(['option' => 'value']);
});

it('should save the blob property', function () {
    $request = new RequestFake();

    // @phpstan-ignore-next-line
    $blobProperty = new BlobProperty([
        'Last-Modified'      => '2021-01-01T00:00:00.0000000Z',
        'ETag'               => '0x8D8D8D8D8D8D8D9',
        'Content-Length'     => '0',
        'Content-Type'       => 'plain/text',
        'Content-MD5'        => 'Q2hlY2sgSW50ZWdyaXR5',
        'Content-Encoding'   => 'gzip',
        'Content-Language'   => 'en-US',
        'x-ms-creation-time' => '2021-01-01T00:00:00.0000000Z',
    ]);

    expect((new BlobPropertyManager($request, $container = 'container', $blob = 'blob.txt'))->save($blobProperty, ['option' => 'value']))
        ->toBeTrue();

    $request->assertPut("{$container}/{$blob}?comp=properties&resttype=blob")
        ->assertSentWithOptions(['option' => 'value'])
        ->assertSentWithHeaders($blobProperty->getPropertiesToSave());
});
