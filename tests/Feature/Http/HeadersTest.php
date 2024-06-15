<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Header;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Sjpereira\AzureStoragePhpSdk\Http\Headers;

uses()->group('http');

it('should throw an exception if header is missing', function (): void {
    expect(new Headers)
        ->nonExistentHeader->toBeNull();
})->throws(InvalidArgumentException::class, 'Invalid header: nonExistentHeader');

it('should set header methods in headers class', function (string $method, string|int $value, string $key): void {
    $headers = array_merge([
        'Content-Encoding'    => null,
        'Content-Language'    => null,
        'Content-Length'      => null,
        'Content-MD5'         => null,
        'Content-Type'        => null,
        'Date'                => null,
        'If-Modified-Since'   => null,
        'If-Match'            => null,
        'If-None-Match'       => null,
        'If-Unmodified-Since' => null,
        'Range'               => null,
    ], [$key => $value]);

    $attribute = str_replace('set', '', $method);
    $attribute[0] = mb_convert_case($attribute[0], MB_CASE_LOWER, 'UTF-8');

    expect((new Headers)->{$method}($value))
        ->toString()->toBe(implode("\n", $headers))
        ->{$attribute}->toBe((string)$value);
})->with([
    'Content Encoding'    => ['setContentEncoding', 'utf-8', 'Content-Encoding'],
    'Content Language'    => ['setContentLanguage', 'en', 'Content-Language'],
    'Content Length'      => ['setContentLength', 100, 'Content-Length'],
    'Content MD5'         => ['setContentMD5', '12345', 'Content-MD5'],
    'Content Type'        => ['setContentType', 'application/xml', 'Content-Type'],
    'Date'                => ['setDate', 'Sat, 15 Jun 2024 00:00:00 GMT', 'Date'],
    'If-Modified-Since'   => ['setIfModifiedSince', 'Fri, 14 Jun 2024 00:00:00 GMT', 'If-Modified-Since'],
    'If-Match'            => ['setIfMatch', 'match', 'If-Match'],
    'If-None-Match'       => ['setIfNoneMatch', 'none', 'If-None-Match'],
    'If-Unmodified-Since' => ['setIfUnmodifiedSince', 'Sun, 16 Jun 2024 00:00:00 GMT', 'If-Unmodified-Since'],
    'Range'               => ['setRange', 'bytes=0-100', 'Range'],
]);

it('should add additional headers', function (){
    $headers = (new Headers)
        ->withAdditionalHeaders(['foo' => 'bar'])
        ->withAdditionalHeaders(['bar' => 'baz']);

    expect($headers)
        ->toArray()->toEqual([
            'foo' => 'bar',
            'bar' => 'baz',
        ]);
});

it('should parse all the given headers into the headers class', function (){
    $headers = [
        'Content-Encoding'    => 'utf-8',
        'Content-Language'    => 'en',
        'Content-Length'      => 100,
        'Content-MD5'         => '12345',
        'Content-Type'        => 'application/xml',
        'Date'                => 'Sat, 15 Jun 2024 00:00:00 GMT',
        'If-Modified-Since'   => 'Fri, 14 Jun 2024 00:00:00 GMT',
        'If-Match'            => 'match',
        'If-None-Match'       => 'none',
        'If-Unmodified-Since' => 'Sun, 16 Jun 2024 00:00:00 GMT',
        'Range'               => 'bytes=0-100',
        'new-header' => 'new-value',
        Resource::CANONICAL_HEADER_PREFIX. 'other' => 'other-value',
    ];

    expect(Headers::parse($headers))
        ->toArray()->toEqual($headers);
});

it('should get all canonical headers from the headers class', function (){
    $additionalHeaders = [
        'another-header' => 'other-value',
        Resource::AUTH_DATE_KEY    =>'Fri, 14 Jun 2024 00:00:00 GMT',
        Resource::CANONICAL_HEADER_PREFIX.'should-create' => false,
        Resource::AUTH_VERSION_KEY => '2021-06-08',
        Resource::CANONICAL_HEADER_PREFIX.'should-delete' => true,
        'new-header' => 'new-value',
    ];

    ksort($additionalHeaders);

    $expected = '';

    foreach ($additionalHeaders as $key => $value) {
        if (strpos($key, Resource::CANONICAL_HEADER_PREFIX) !== 0) {
            continue;
        }

        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        }

        $expected .= "{$key}:{$value}\n";
    }


    expect((new Headers)->withAdditionalHeaders($additionalHeaders))
        ->getCanonicalHeaders()->toBe(rtrim($expected, "\n"));
});