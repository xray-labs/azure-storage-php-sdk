<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities;

use DOMDocument;
use GuzzleHttp\Exception\RequestException;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container\Containers;
use Sjpereira\AzureStoragePhpSdk\Http\{Request};
use Sjpereira\AzureStoragePhpSdk\Parsers\Contracts\Parser;

class Account
{
    public function __construct(protected Request $request, protected Parser $parser)
    {
        //
    }

    public function listContainers(array $options = []): Containers
    {
        try {
            $response = $this->request
                ->withOptions($options)
                ->get('?comp=list')
                ->getBody()
                ->getContents();

            /** @var DOMDocument $parsed */
            $parsed = $this->parser->parse($response);

            return new Containers($parsed['Containers']['Container'] ?? []);
        } catch (RequestException $e) {
            throw $e; // TODO: Create Custom Exception
        }
    }
}
