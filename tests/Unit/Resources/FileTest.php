<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

pest()->group('resources');
covers(File::class);

it('should not be able to create a file without a name', function () {
    $file = new File('', 'content');

    expect($file)->toBeInstanceOf(File::class);
})->throws(InvalidArgumentException::class, '[name] cannot be empty');

it('should get file information', function (string $method, string|bool|int|DateTimeImmutable $expected) {
    /** @phpstan-ignore-next-line */
    $file = new File('name', 'content', [
        'Content-Type'          => 'text/plain',
        'Content-Length'        => 7,
        'Content-MD5'           => 'md5',
        'Last-Modified'         => '2021-10-02T00:00:00.0000000Z',
        'Accept-Ranges'         => 'bytes',
        'ETag'                  => 'etag',
        'Vary'                  => 'Accept-Encoding',
        'Server'                => 'Windows-Azure-Blob/1.0 Microsoft-HTTPAPI/2.0',
        'x-ms-request-id'       => 'request-id',
        'x-ms-version'          => '2019-02-02',
        'x-ms-creation-time'    => '2021-01-01T00:00:00.0000000Z',
        'x-ms-lease-status'     => 'unlocked',
        'x-ms-lease-state'      => 'available',
        'x-ms-blob-type'        => 'BlockBlob',
        'x-ms-server-encrypted' => 'true',
        'Date'                  => '2021-10-05T00:00:00.0000000Z',
    ]);

    $formatValue = fn (string|bool|int|DateTimeImmutable $value) => $value instanceof DateTimeImmutable
        ? $value->format('Y-m-d')
        : $value;

    expect($formatValue($file->{$method}()))->toBe($formatValue($expected));
})->with([
    'Filename'       => ['getFilename', 'name'],
    'Content'        => ['getContent', 'content'],
    'Content Length' => ['getContentLength', 7],
    'Content Type'   => ['getContentType', 'text/plain'],
    'Content MD5'    => ['getContentMd5', 'md5'],
    'Last Modified'  => ['getLastModified', fn () => new DateTimeImmutable('2021-10-02T00:00:00.0000000Z')],
    'Accept Ranges'  => ['getAcceptRanges', 'bytes'],
    'ETag'           => ['getETag', 'etag'],
    'Vary'           => ['getVary', 'Accept-Encoding'],
    'Server'         => ['getServer', 'Windows-Azure-Blob/1.0 Microsoft-HTTPAPI/2.0'],
    'Request'        => ['getRequestId', 'request-id'],
    'Version'        => ['getVersion', '2019-02-02'],
    'Created'        => ['getCreationTime', fn () => new DateTimeImmutable('2021-01-01T00:00:00.0000000Z')],
    'Lease'          => ['getLeaseStatus', 'unlocked'],
    'State'          => ['getLeaseState', 'available'],
    'Type'           => ['getBlobType', 'BlockBlob'],
    'Encrypted'      => ['getServerEncrypted', true],
    'Date'           => ['getDate', fn () => new DateTimeImmutable('2021-10-05T00:00:00.0000000Z')],
]);

it('should detect the content type based on the content', function () {
    $file = new File('name', 'content');

    expect($file->getContentType())
        ->toBe('text/plain');
});
