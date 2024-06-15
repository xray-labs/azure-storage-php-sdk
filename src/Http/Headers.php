<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Http;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Arrayable, Stringable};
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

/**
 * @property-read ?string $contentEncoding
 * @property-read ?string $contentLanguage
 * @property-read ?int $contentLength
 * @property-read ?string $contentMD5
 * @property-read ?string $contentType
 * @property-read ?string $date
 * @property-read ?string $ifModifiedSince
 * @property-read ?string $ifMatch
 * @property-read ?string $ifNoneMatch
 * @property-read ?string $ifUnmodifiedSince
 * @property-read ?string $range
 *
 * @implements Arrayable<string, int|string|null>
 *
 * @see https://docs.microsoft.com/en-us/rest/api/storageservices/put-blob
 */
final class Headers implements Stringable, Arrayable
{
    /** @var array<string, int|string|null> $headers */
    protected array $headers = [
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
    ];

    /** @var array<string, scalar> $additionalHeaders */
    public array $additionalHeaders = [];

    /** @param array<string, scalar> $headers*/
    public static function parse(array $headers): static
    {
        $instance = new static();

        $additionalHeaders = [];

        foreach ($headers as $name => $value) {
            $method = 'set' . preg_replace('/[^\w]/', '', mb_convert_case($name, MB_CASE_LOWER, 'UTF-8'));

            if (!method_exists($instance, $method)) {
                $additionalHeaders[$name] = $value;

                continue;
            }

            $instance->$method($value);
        }

        return $instance->withAdditionalHeaders($additionalHeaders);
    }

    public function __get(string $attribute): ?string
    {
        $name = str_camel_to_header($attribute);

        if (!array_key_exists($name, $this->headers)) {
            throw InvalidArgumentException::create("Invalid header: $attribute");
        }

        return (string) $this->headers[$name];
    }

    public function toString(): string
    {
        return implode("\n", $this->headers);
    }

    public function getCanonicalHeaders(): string
    {
        $additionalHeaders = $this->additionalHeaders;
        $canonicalHeaders  = '';

        ksort($additionalHeaders);

        foreach ($additionalHeaders as $key => $value) {
            $keyLower = mb_convert_case($key, MB_CASE_LOWER, 'UTF-8');

            if (strpos($keyLower, Resource::CANONICAL_HEADER_PREFIX) !== 0) {
                continue;
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $canonicalHeaders .= "{$keyLower}:{$value}\n";
        }

        return rtrim($canonicalHeaders, "\n");
    }

    /** @param array<string, scalar> $additionalHeaders */
    public function withAdditionalHeaders(array $additionalHeaders = []): static
    {
        $this->additionalHeaders = array_merge($this->additionalHeaders, $additionalHeaders);

        return $this;
    }

    public function setContentEncoding(string $contentEncoding): static
    {
        $this->headers['Content-Encoding'] = $contentEncoding;

        return $this;
    }

    public function setContentLanguage(string $contentLanguage): static
    {
        $this->headers['Content-Language'] = $contentLanguage;

        return $this;

    }

    public function setContentLength(int $contentLength): static
    {
        $this->headers['Content-Length'] = $contentLength;

        return $this;
    }

    public function setContentMD5(string $contentMD5): static
    {
        $this->headers['Content-MD5'] = $contentMD5;

        return $this;
    }

    public function setContentType(string $contentType): static
    {
        $this->headers['Content-Type'] = $contentType;

        return $this;
    }

    public function setDate(string $date): static
    {
        $this->headers['Date'] = $date;

        return $this;
    }

    public function setIfModifiedSince(string $ifModifiedSince): static
    {
        $this->headers['If-Modified-Since'] = $ifModifiedSince;

        return $this;
    }

    public function setIfMatch(string $ifMatch): static
    {
        $this->headers['If-Match'] = $ifMatch;

        return $this;
    }

    public function setIfNoneMatch(string $ifNoneMatch): static
    {
        $this->headers['If-None-Match'] = $ifNoneMatch;

        return $this;
    }

    public function setIfUnmodifiedSince(string $ifUnmodifiedSince): static
    {
        $this->headers['If-Unmodified-Since'] = $ifUnmodifiedSince;

        return $this;
    }

    public function setRange(string $range): static
    {
        $this->headers['Range'] = $range;

        return $this;
    }

    public function toArray(): array
    {
        $headers = array_filter($this->headers);

        return array_merge($headers, $this->additionalHeaders);
    }
}
