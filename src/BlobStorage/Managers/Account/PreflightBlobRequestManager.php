<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Account;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\{Request, Response};
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\{RequestException};

class PreflightBlobRequestManager implements Manager
{
    public function __construct(protected Request $request)
    {
        //
    }

    public function delete(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::DELETE, $origin, $headers);
    }

    public function get(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::GET, $origin, $headers);
    }

    public function head(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::HEAD, $origin, $headers);
    }

    public function merge(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::MERGE, $origin, $headers);
    }

    public function post(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::POST, $origin, $headers);
    }

    public function options(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::OPTIONS, $origin, $headers);
    }

    public function put(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::PUT, $origin, $headers);
    }

    public function patch(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::PATCH, $origin, $headers);
    }

    protected function request(HttpVerb $verb, string $origin, array $headers = []): Response
    {
        $options = [
            Resource::ACCESS_CONTROL_ORIGIN_KEY         => $origin,
            Resource::ACCESS_CONTROL_REQUEST_METHOD_KEY => $verb->value,
        ];

        if ($headers) {
            $options[Resource::ACCESS_CONTROL_REQUEST_HEADERS_KEY] = $headers;
        }

        try {
            return $this->request
                ->withHeaders($options)
                ->withoutAuthentication()
                ->options('');
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }
}
