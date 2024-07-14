<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobTag;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobTagManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Http\Response as BaseResponse;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'blobs');

it('should get the blob\'s tags', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <Tags>
        <TagSet>
            <Tag>
                <Key>value</Key>
                <Key2>value2</Key2>
            </Tag>
        </TagSet>
    </Tags>
    XML;

    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake($body, headers: [
            'Content-Length'  => ['10'],
            'Content-Type'    => ['application/xml'],
            'Vary'            => ['*'],
            'Server'          => ['Windows-Azure-Blob/1.0'],
            'x-ms-request-id' => ['1'],
            'x-ms-version'    => ['2021-06-08'],
            'Date'            => ['2021-06-08T00:00:00.0000000Z'],
        ]));

    $result = (new BlobTagManager($request, $container = 'container', $blob = 'blob'))->get(['key' => 'value']);

    expect($result)
        ->toBeInstanceOf(BlobTag::class)
        ->contentLength->toBe(10)
        ->contentType->toBe('application/xml')
        ->vary->toBe('*')
        ->server->toBe('Windows-Azure-Blob/1.0')
        ->xMsRequestId->toBe('1')
        ->xMsVersion->toBe('2021-06-08')
        ->date->format('Y-m-d\TH:i:s')->toBe('2021-06-08T00:00:00')
        ->and($result->tags)
        ->toBeArray()
        ->toHaveCount(2)
        ->toBe(['Key' => 'value', 'Key2' => 'value2']);

    $request->assertGet("{$container}/{$blob}?resttype=blob&comp=tags")
        ->assertSentWithOptions(['key' => 'value']);
});

it('should put a new blob tag', function () {
    $request = (new RequestFake(new Config(new SharedKeyAuth('account', 'key'))))
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_NO_CONTENT));

    $blobTag = new BlobTag(['key' => 'value']);

    $result = (new BlobTagManager($request, $container = 'container', $blob = 'blob'))
        ->put($blobTag, ['some' => 'value']);

    expect($result)->toBeTrue();

    $request->assertPut("{$container}/{$blob}?resttype=blob&comp=tags")
        ->assertSentWithOptions(['some' => 'value'])
        ->assertSentWithHeaders([
            Resource::CONTENT_TYPE => 'application/xml; charset=UTF-8',
        ]);
});
