<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{BlobTag};
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

uses()->group('entities', 'blobs');

it('should mount the blob tags', function () {
    $blobTag = new BlobTag([
        ['Key' => 'key', 'Value' => 'value'],
        'key2' => 'value2',
    ]);

    expect($blobTag)
        ->tags->toEqual([
            'key'  => 'value',
            'key2' => 'value2',
        ]);
});

it('should throw an exception if the tag structure is invalid', function (array $tag) {
    $blobTag = new BlobTag([...$tag]);

    expect($blobTag)
        ->toBeInstanceOf(BlobTag::class);
})->with([
    'Invalid Key Value Pair' => [[['value' => 'key']]],
    'Invalid Array Tag'      => [['key' => ['value']]],
])->throws(InvalidArgumentException::class, 'Invalid tag structure');

it('should throw an exception if the tag key has more than 128 characters', function () {
    $key = str_repeat('a', 129);

    expect(fn () => new BlobTag([$key => 'value']))
        ->toThrow(InvalidArgumentException::class, "Invalid tag key: {$key}. Tag keys cannot be more than 128 characters in length.");
});

it('should throw an exception if the tag key is not alphanumeric and some special characters', function () {
    $key = '#test key';

    expect(fn () => new BlobTag([$key => 'value']))
        ->toThrow(InvalidArgumentException::class, "Invalid tag key: {$key}. Only alphanumeric characters and '+ . / : = _' are allowed.");
});

it('should throw an exception if the tag value has more than 256 characters', function () {
    $value = str_repeat('a', 257);

    expect(fn () => new BlobTag(['key' => $value]))
        ->toThrow(InvalidArgumentException::class, "Invalid tag value: {$value}. Tag values cannot be more than 256 characters in length.");
});

it('should throw an exception if the tag value is not alphanumeric and some special characters', function () {
    $value = '#test value';

    expect(fn () => new BlobTag(['key' => $value]))
        ->toThrow(InvalidArgumentException::class, "Invalid tag value: {$value}. Only alphanumeric characters and '+ . / : = _' are allowed.");
});

it('should find a tag property', function (string $property, int|string|null $value) {
    $blobTag = new BlobTag([
        ['Key' => 'key', 'Value' => 'value'],
        'key2' => 'value2',
    ], ['Content-Length' => '10']);

    expect($blobTag->find($property))->toBe($value);
})->with([
    'Get Existing Property'     => ['contentLength', 10],
    'Get Non-Existing Property' => ['server', null],
    'Get Existing Tag'          => ['key', 'value'],
    'Get Non-Existing Tag'      => ['key3', null],
]);

it('should check if a tag property exists', function (string $property, bool $exists) {
    $blobTag = new BlobTag([
        ['Key' => 'key', 'Value' => 'value'],
        'key2' => 'value2',
    ], ['Content-Length' => '10']);

    expect($blobTag->has($property))->toBe($exists);
})->with([
    'Check Existing Property'     => ['contentLength', true],
    'Check Non-Existing Property' => ['server', false],
    'Check Existing Tag'          => ['key', true],
    'Check Non-Existing Tag'      => ['key3', false],
]);

it('should convert the blob tag to xml', function () {
    $blobTag = new BlobTag([
        ['Key' => 'key', 'Value' => 'value'],
        'key2' => 'value2',
    ]);

    $xml = <<<XML
    <?xml version="1.0"?>
    <Tags>
        <TagSet>
            <Tag><Key>key</Key><Value>value</Value></Tag>
            <Tag><Key>key2</Key><Value>value2</Value></Tag>
        </TagSet>
    </Tags>
    XML;

    expect(preg_replace('/\s/m', '', $blobTag->toXml()))
        ->toBe(preg_replace('/\s/m', '', $xml));
});
