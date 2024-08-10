<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobMetadata;

uses()->group('entities', 'blobs');

it('should get the metadata property', function (string $property, string $value) {
    $metadata = new BlobMetadata([
        'x-ms-meta-test'  => 'valid',
        'x-ms-meta-test2' => 'valid2',
    ], [
        'Content-Length'  => '10',
        'Last-Modified'   => 'now',
        'ETag'            => 'etag',
        'Vary'            => 'Accept-Encoding',
        'Server'          => 'xray',
        'x-ms-request-id' => 'request-id',
        'x-ms-version'    => '1.0',
        'Date'            => 'now',
    ]);

    expect($metadata->get($property))
        ->toBe($value);
})->with([
    'Get Existing Property' => ['eTag', 'etag'],
    'Get Metadata Property' => ['test', 'valid'],
]);

it('should check if the metadata property has in the metadata object', function (string $property, bool $expected) {
    $metadata = new BlobMetadata([
        'x-ms-meta-test'  => 'valid',
        'x-ms-meta-test2' => 'valid2',
    ], [
        'Content-Length' => '10',
        'Last-Modified'  => 'now',
        'ETag'           => 'etag',
        'Vary'           => 'Accept-Encoding',
    ]);

    expect($metadata->has($property))
        ->toBe($expected);
})->with([
    'Check Option Property Exists'    => ['eTag', true],
    'Check Option Property Missing'   => ['server', false],
    'Check Metadata Property Exists'  => ['test', true],
    'Check Metadata Property Missing' => ['test3', false],
]);

it('should get metadata properties to save', function () {
    $metadata = new BlobMetadata([
        'x-ms-meta-test'  => 'valid',
        'test2'           => 'valid2',
        'x-ms-meta-test3' => null,
        'test4'           => null,
    ]);

    expect($metadata->getMetadataToSave())
        ->toEqual([
            'x-ms-meta-test'  => 'valid',
            'x-ms-meta-test2' => 'valid2',
        ]);
});
