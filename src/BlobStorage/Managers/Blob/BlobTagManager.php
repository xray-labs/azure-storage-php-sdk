<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobTag;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException;

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
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }

        /** @var array{TagSet: array{Tag: array<string, scalar>}} $response */
        $response = $this->request->getConfig()->parser->parse($body);

        /** @var array<int, array{Key: string, Value: string}> $tags */
        $tags = $response['TagSet']['Tag'];

        return new BlobTag($tags, (array) $headers);
    }

    /** @param array<string, scalar> $options */
    public function put(BlobTag $blobTag, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::CONTENT_LENGTH => strlen($xml = $blobTag->toXml()),
                    Resource::CONTENT_TYPE   => 'application/xml; charset=UTF-8',
                ])
                ->put("{$this->containerName}/{$this->blobName}?resttype=blob&comp=tags", $xml)
                ->isNoContent();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}
