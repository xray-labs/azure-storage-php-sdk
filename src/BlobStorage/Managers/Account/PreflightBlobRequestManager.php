<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Account;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Contracts\Http\{Request, Response};
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;

class PreflightBlobRequestManager implements Manager
{
    public function __construct(protected Request $request)
    {
        //
    }

    /** @param array<string, scalar> $headers */
    public function delete(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::DELETE, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function get(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::GET, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function head(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::HEAD, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function merge(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::MERGE, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function post(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::POST, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function options(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::OPTIONS, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function put(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::PUT, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    public function patch(string $origin, array $headers = []): Response
    {
        return $this->request(HttpVerb::PATCH, $origin, $headers);
    }

    /** @param array<string, scalar> $headers */
    protected function request(HttpVerb $verb, string $origin, array $headers = []): Response
    {
        $options = [
            Resource::ORIGIN                        => $origin,
            Resource::ACCESS_CONTROL_REQUEST_METHOD => $verb->value,
        ];

        if ($headers) {
            $options[Resource::ACCESS_CONTROL_REQUEST_HEADERS] = implode(',', $headers);
        }

        try {
            return $this->request
                ->withHeaders($options)
                ->withoutAuthentication()
                ->options('');

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }
}
