<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobProperty;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobPropertyManager;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'blobs');

it('should get the blob\'s properties', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
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
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

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
