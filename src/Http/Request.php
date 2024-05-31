<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Http;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};

class Request
{
    /**
     * Undocumented variable
     *
     * @var array<string, scalar>
     */
    protected array $options = [];

    /**
     * Undocumented variable
     *
     * @var array<string, scalar>
     */
    protected array $headers = [];

    public function __construct(
        protected ClientInterface $client,
        public Config $config,
    ) {
        //
    }

    /**
     * Undocumented variable
     *
     * @param array<string, scalar> $options
     */
    public function withOptions(array $options = []): static
    {
        $this->options = array_merge($this->options, $options);

        return $this;
    }

    /**
     * Undocumented variable
     *
     * @param array<string, scalar> $headers
     */
    public function withHeaders(array $headers = []): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    public function get(string $endpoint): ResponseInterface
    {
        $options = $this->getOptions(
            $verb = HttpVerb::GET,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return $this->client->request($verb->value, $uri, $options);
    }

    public function put(string $endpoint): ResponseInterface
    {
        $options = $this->getOptions(
            $verb = HttpVerb::PUT,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return $this->client->request($verb->value, $uri, $options);
    }

    public function delete(string $endpoint): ResponseInterface
    {
        $options = $this->getOptions(
            $verb = HttpVerb::DELETE,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return $this->client->request($verb->value, $uri, $options);
    }

    /**
     * Undocumented function
     *
     * @param HttpVerb $verb
     * @param string $resource
     * @return array<string, mixed>
     */
    protected function getOptions(HttpVerb $verb, string $resource): array
    {
        $options = $this->options;

        $headers = Headers::parse(array_merge($this->headers, [
            Resource::AUTH_DATE_KEY    => $this->config->auth->getDate(),
            Resource::AUTH_VERSION_KEY => Resource::VERSION,
        ]));

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
