<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\Http;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};

class Request
{
    protected array $options = [];

    protected array $headers = [];

    public function __construct(
        protected ClientInterface $client,
        protected Config $config,
        protected SharedKeyAuth $auth,
    ) {
        //
    }

    public function withOptions(array $options = []): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    public function withHeaders(array $headers = []): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function get(string $endpoint): ResponseInterface
    {
        $options = $this->getOptions(
            HttpVerb::GET,
            $this->getResource($endpoint),
        );

        return $this->client->request(
            'GET',
            $this->uri($endpoint),
            $options,
        );
    }

    protected function getResource(string $endpoint): string
    {
        return str_replace(['?', '='], ['', ':'], $endpoint);
    }

    protected function getOptions(HttpVerb $verb, string $resource): array
    {
        $options = $this->options;
        $headers = Headers::parse($this->headers);

        $options['headers'] = array_merge($this->headers, [
            Resource::AUTH_DATE_KEY    => $this->auth->getDate(),
            Resource::AUTH_VERSION_KEY => Resource::VERSION,
            Resource::AUTH_HEADER_KEY  => $this->auth->getAuthentication($verb, $headers, $resource),
        ]);

        return $options;
    }

    protected function uri(?string $endpoint = null): string
    {
        return "https://{$this->config->account}.blob.core.windows.net/{$endpoint}";
    }
}
