<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobProperty;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

/**
 * @phpstan-import-type BlobPropertyHeaders from BlobProperty
 */
class BlobPropertyManager implements Manager
{
    public function __construct(
        protected Request $request,
        protected string $containerName,
        protected string $blobName,
    ) {
        //
    }

    /** @param array<string, scalar> $options */
    public function get(array $options = []): BlobProperty
    {
        try {
            /** @var BlobPropertyHeaders $headers */
            $headers = $this->request
                ->withOptions($options)
                ->get("{$this->containerName}/{$this->blobName}?resttype=blob")
                ->getHeaders();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd

        $headers = (array) $headers;
        array_walk($headers, fn (string|array &$value) => $value = is_array($value) ? current($value) : $value); // @phpstan-ignore-line

        return new BlobProperty($headers);
    }

    /** @param array<string, scalar> $options */
    public function save(BlobProperty $blobProperty, array $options = []): bool
    {
        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders($blobProperty->getPropertiesToSave())
                ->put("{$this->containerName}/{$this->blobName}?comp=properties&resttype=blob")
                ->isOk();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}
