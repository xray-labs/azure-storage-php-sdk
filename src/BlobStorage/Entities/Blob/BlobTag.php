<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use DateTimeImmutable;
use Sjpereira\AzureStoragePhpSdk\Contracts\Xmlable;
use Sjpereira\AzureStoragePhpSdk\Converter\XmlConverter;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

/**
 * @phpstan-type BlobTagHeaders array{Content-Length?: string, Content-Type?: string, Vary?: string, Server?: string, x-ms-request-id?: string, x-ms-version?: string, Date?: string}
 */
final readonly class BlobTag implements Xmlable
{
    /** @var array<string, scalar> */
    public array $tags;

    public ?int $contentLength;

    public ?string $contentType;

    public ?string $vary;

    public ?string $server;

    public ?string $xMsRequestId;

    public ?string $xMsVersion;

    public ?DateTimeImmutable $date;

    /**
     * @param array<int, array{Key: string, Value: string}>|array<string, scalar> $tags
     * @param BlobTagHeaders $options
    */
    public function __construct(array $tags = [], array $options = [])
    {
        $this->contentLength = isset($options['Content-Length']) ? (int) $options['Content-Length'] : null;
        $this->contentType   = $options['Content-Type'] ?? null;
        $this->vary          = $options['Vary'] ?? null;
        $this->server        = $options['Server'] ?? null;
        $this->xMsRequestId  = $options['x-ms-request-id'] ?? null;
        $this->xMsVersion    = $options['x-ms-version'] ?? null;
        $this->date          = isset($options['Date']) ? new DateTimeImmutable($options['Date']) : null;

        $this->tags = $this->mountTags($tags);
    }

    public function find(string $name): mixed
    {
        if (property_exists($this, $name)) {
            return $this->{$name};
        }

        $this->validateTagKey($name);

        return $this->tags[$name] ?? null;
    }

    public function has(string $name): bool
    {
        if (property_exists($this, $name)) {
            return $this->{$name} !== null;
        }

        $this->validateTagKey($name);

        return isset($this->tags[$name]);
    }

    public function toXml(): string
    {
        $tags = [];

        foreach ($this->tags as $key => $value) {
            $tags[] = [
                'Tag' => [
                    'Key'   => $key,
                    'Value' => $value,
                ],
            ];
        }

        return (new XmlConverter())->convert([
            'Tags' => [
                'TagSet' => $tags,
            ],
        ]);
    }

    protected function validateTagKey(string $key): void
    {
        $message = "Invalid tag key: {$key}.";

        if (strlen($key) > 128) {
            throw InvalidArgumentException::create("{$message} Tag keys cannot be more than 128 characters in length.");
        }

        if (!preg_match('/^[a-zA-Z0-9\+\.\-\/:=_]*$/', $key)) {
            throw InvalidArgumentException::create("{$message} Only alphanumeric characters and '+ . / : = _' are allowed.");
        }
    }

    protected function validateTagValue(string $value): void
    {
        $message = "Invalid tag value: {$value}.";

        if (strlen($value) > 256) {
            throw InvalidArgumentException::create("{$message} Tag values cannot be more than 256 characters in length.");
        }

        if (!preg_match('/^[a-zA-Z0-9\+\.\-\/:=_]*$/', $value)) {
            throw InvalidArgumentException::create("{$message} Only alphanumeric characters and '+ . / : = _' are allowed.");
        }
    }

    /**
     * @param array<int, array{Key: string, Value: string}>|array<string, scalar> $tags
     * @return array<string, scalar>
     */
    protected function mountTags(array $tags): array
    {
        $tagsParsed = [];

        foreach ($tags as $arrayKey => $tag) {
            if (is_int($arrayKey) && is_array($tag)) {
                /** @var array{Key?: string, Value?: string} $tag */
                if (!array_key_exists('Key', $tag) || !array_key_exists('Value', $tag)) {
                    throw InvalidArgumentException::create('Invalid tag structure.');
                }

                $this->validateTagKey($key = $tag['Key']);

                $this->validateTagValue($tag['Value']);

                $tagsParsed[$key] = $tag['Value'];

                continue;
            }

            if (is_array($tag)) {
                throw InvalidArgumentException::create('Invalid tag structure.');
            }

            $this->validateTagKey($key = (string) $arrayKey);
            $this->validateTagValue((string) $tag);

            $tagsParsed[$key] = $tag;
        }

        return $tagsParsed;
    }
}
