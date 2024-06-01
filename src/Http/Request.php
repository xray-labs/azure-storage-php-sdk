<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Http;

use GuzzleHttp\ClientInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};

class Request
{
    /** @var array<string, scalar> */
    protected array $options = [];

    /** @var array<string, scalar> */
    protected array $headers = [];

    public function __construct(
        protected ClientInterface $client,
        public Config $config,
    ) {
        //
    }

    /** @param array<string, scalar> $options */
    public function withOptions(array $options = []): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /** @param array<string, scalar> $headers */
    public function withHeaders(array $headers = []): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function get(string $endpoint): Response
    {
        $options = $this->getOptions(
            $verb = HttpVerb::GET,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    public function put(string $endpoint, string $body = ''): Response
    {
        $options = $this->getOptions(
            $verb = HttpVerb::PUT,
            Resource::canonicalize($uri = $this->uri($endpoint)),
            $body,
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    public function delete(string $endpoint): Response
    {
        $options = $this->getOptions(
            $verb = HttpVerb::DELETE,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    protected function getOptions(HttpVerb $verb, string $resource, string $body = ''): array
    {
        $options = $this->options;

        $headers = Headers::parse(array_merge($this->headers, [
            Resource::AUTH_DATE_KEY    => $this->config->auth->getDate(),
            Resource::AUTH_VERSION_KEY => Resource::VERSION,
        ]));

        if (!empty($body)) {
            $options['body'] = $body;
            $headers->setContentLength(strlen($body));
        }

        $options['headers'] = $headers->withAdditionalHeaders([
            Resource::AUTH_HEADER_KEY => $this->config->auth->getAuthentication($verb, $headers, $resource),
        ])->toArray();

        return $options;
    }

    protected function uri(?string $endpoint = null): string
    {
        return "https://{$this->config->account}.blob.core.windows.net/{$endpoint}";
    }
}
