<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobTag;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobTagHeaders from BlobTag
 */
class BlobTagManager implements Manager
{
    public function __construct(
        protected Request $request,
        protected string $containerName,
        protected string $blobName,
    ) {
        //
    }

    /** @param array<string, scalar> $options */
    public function get(array $options = []): BlobTag
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/{$this->blobName}?resttype=blob&comp=tags");

            $body = $response->getBody();

            /** @var BlobTagHeaders $headers */
            $headers = $response->getHeaders();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        /** @var array{TagSet: array{Tag: array<string, scalar>}} $parsed */
        $parsed = $this->request->getConfig()->parser->parse($body);

        /** @var array<int, array{Key: string, Value: string}> $tags */
        $tags    = $parsed['TagSet']['Tag'];
        $headers = (array) $headers;

        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value); // @phpstan-ignore-line

        return azure_app(BlobTag::class, ['tags' => $tags, 'options' => $headers]);
    }

    /** @param array<string, scalar> $options */
    public function put(BlobTag $blobTag, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::CONTENT_TYPE => 'application/xml; charset=UTF-8',
                ])
                ->put("{$this->containerName}/{$this->blobName}?resttype=blob&comp=tags", $blobTag->toXml())
                ->isNoContent();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}
