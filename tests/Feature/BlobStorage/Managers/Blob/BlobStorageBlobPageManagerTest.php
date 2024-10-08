<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Enums\BlobType;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\{BlobManager, BlobPageManager};
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Xray\AzureStoragePhpSdk\Fakes\Http\{RequestFake, ResponseFake};
use Xray\AzureStoragePhpSdk\Http\Response as BaseResponse;

pest()->group('blob-storage', 'managers', 'blobs');
covers(BlobPageManager::class);

it('should throw an exception if the page is out of boundary', function () {
    $request = new RequestFake();

    expect((new BlobPageManager($request, 'container'))->create('blob', 1025));
})->throws(InvalidArgumentException::class, 'Page blob size must be aligned to a 512-byte boundary.');

it('should create a new blob page', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $name    = 'blob';
    $length  = 1024;
    $options = ['foo' => 'bar'];
    $headers = ['baz' => 'qux'];

    expect((new BlobPageManager($request, $container = 'container'))->create($name, $length, $options, $headers))
        ->toBeTrue();

    $request->assertPut("{$container}/{$name}?resttype=blob")
        ->assertSentWithOptions($options)
        ->assertSentWithHeaders(array_merge([
            Resource::BLOB_TYPE           => BlobType::PAGE->value,
            Resource::BLOB_CONTENT_LENGTH => $length,
        ], $headers));
});

it('should not append a page if the page size is invalid', function (int $startPage, int $endPage, string $message) {
    $request = (new RequestFake());

    $file    = new File('name', str_repeat('a', 1536));
    $options = ['foo' => 'bar'];

    expect(fn () => (new BlobPageManager($request, 'container'))->append($file, $startPage, $endPage, $options))
        ->toThrow(InvalidArgumentException::class, $message);
})->with([
    'Start Byte Negative'                    => [-1, 2, 'The start page should be greater than 0'],
    'Start Byte Greater Than End Byte'       => [2, 1, 'The end page should be greater than the start page'],
    'End Byte Less Than File Content Length' => [1, 2, 'The file size is greater than the page range'],
]);

it('should append an additional page', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $file      = new File('name', str_repeat('a', 1024));
    $startPage = 1;
    $endPage   = 2;
    $options   = ['foo' => 'bar'];

    expect((new BlobPageManager($request, $container = 'container'))->append($file, $startPage, $endPage, $options))
        ->toBeTrue();

    $request->assertPut("{$container}/{$file->getFilename()}?resttype=blob&comp=page", $file->getContent())
        ->assertSentWithOptions($options)
        ->assertSentWithHeaders([
            Resource::PAGE_WRITE     => 'update',
            Resource::RANGE          => 'bytes=0-1023',
            Resource::CONTENT_TYPE   => $file->getContentType(),
            Resource::CONTENT_LENGTH => $file->getContentLength(),
            Resource::CONTENT_MD5    => $file->getContentMD5(),
        ]);
});

it('should put a new blob page', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $file    = new File('name', str_repeat('a', 1024));
    $options = ['foo' => 'bar'];

    expect((new BlobPageManager($request, $container = 'container'))->put($file, $options))
        ->toBeTrue();

    $request->assertPut("{$container}/{$file->getFilename()}?resttype=blob&comp=page", $file->getContent())
        ->assertSentWithOptions($options)
        ->assertSentWithHeaders([
            Resource::BLOB_TYPE           => BlobType::PAGE->value,
            Resource::BLOB_CONTENT_LENGTH => $file->getContentLength(),
            Resource::CONTENT_TYPE        => $file->getContentType(),
            Resource::CONTENT_MD5         => $file->getContentMD5(),
            Resource::PAGE_WRITE          => 'update',
            Resource::RANGE               => 'bytes=0-1023',
            Resource::CONTENT_LENGTH      => $file->getContentLength(),
        ]);
});

it('should clear a blob page', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $name      = 'blob';
    $startPage = 1;
    $endPage   = 2;
    $options   = ['foo' => 'bar'];

    expect((new BlobPageManager($request, $container = 'container'))->clear($name, $startPage, $endPage, $options))
        ->toBeTrue();

    $request->assertPut("{$container}/{$name}?resttype=blob&comp=page")
        ->assertSentWithOptions($options)
        ->assertSentWithHeaders([
            Resource::PAGE_WRITE => 'clear',
            Resource::RANGE      => 'bytes=0-1023',
        ]);
});

it('should clear all the file\'s pages', function () {
    $blobRequest = (new RequestFake())
        ->withFakeResponse(new ResponseFake(str_repeat('a', 1536)));

    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    $name    = 'blob';
    $options = ['foo' => 'bar'];

    $manager = (new BlobPageManager($request, $container = 'container'))
        ->setManager(new BlobManager($blobRequest, $container));

    expect(($manager->clearAll($name, $options)))
        ->toBeTrue();

    $request->assertPut("{$container}/{$name}?resttype=blob&comp=page")
        ->assertSentWithOptions($options)
        ->assertSentWithHeaders([
            Resource::PAGE_WRITE => 'clear',
            Resource::RANGE      => 'bytes=0-1535',
        ]);
});
