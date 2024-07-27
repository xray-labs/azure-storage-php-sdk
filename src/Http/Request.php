<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Http;

use Closure;
use GuzzleHttp\{Client, ClientInterface};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Xray\AzureStoragePhpSdk\Contracts\Http\{Request as RequestContract, Response as ResponseContract};

class Request implements RequestContract
{
    protected ClientInterface $client;

    /** @var array<string, scalar> */
    protected array $options = [];

    /** @var array<string, scalar> */
    protected array $headers = [];

    protected ?Closure $usingAccountCallback = null;

    protected bool $shouldAuthenticate = true;

    public function __construct(
        public Config $config,
        ?ClientInterface $client = null,
        public string $protocol = 'https',
        public string $baseDomain = 'blob.core.windows.net'
    ) {
        $this->client = $client ?? new Client();
    }

    public function usingAccount(Closure $callback): static
    {
        $this->usingAccountCallback = $callback;

        return $this;
    }

    public function getConfig(): Config
    {
        return $this->config;
    }

    public function withAuthentication(bool $shouldAuthenticate = true): static
    {
        $this->shouldAuthenticate = $shouldAuthenticate;

        return $this;
    }

    public function withoutAuthentication(): static
    {
        return $this->withAuthentication(false);
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

    public function get(string $endpoint): ResponseContract
    {
        $options = $this->getOptions(
            $verb = HttpVerb::GET,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    public function post(string $endpoint, string $body = ''): ResponseContract
    {
        $options = $this->getOptions(
            $verb = HttpVerb::POST,
            Resource::canonicalize($uri = $this->uri($endpoint)),
            $body,
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    public function put(string $endpoint, string $body = ''): ResponseContract
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

    public function delete(string $endpoint): ResponseContract
    {
        $options = $this->getOptions(
            $verb = HttpVerb::DELETE,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    public function options(string $endpoint): ResponseContract
    {
        $options = $this->getOptions(
            $verb = HttpVerb::OPTIONS,
            Resource::canonicalize($uri = $this->uri($endpoint)),
        );

        return Response::createFromGuzzleResponse(
            $this->client->request($verb->value, $uri, $options)
        );
    }

    public function uri(?string $endpoint = null): string
    {
        $account = $this->config->auth->getAccount();

        if (!is_null($this->usingAccountCallback)) {
            $account = call_user_func($this->usingAccountCallback, $account);

            $this->usingAccountCallback = null;
        }

        if (!is_null($endpoint)) {
            [$endpoint, $params] = array_pad(explode('?', $endpoint, 2), 2, '');

            $endpoint = implode('/', array_map('rawurlencode', explode('/', $endpoint))) . "?{$params}";
        }

        return "{$this->protocol}://{$account}.{$this->baseDomain}/{$endpoint}";
    }

    /** @return array<string, mixed> */
    protected function getOptions(HttpVerb $verb, string $resource, string $body = ''): array
    {
        $options = $this->options;

        $headers = Headers::parse(array_merge($this->headers, [
            Resource::AUTH_DATE    => $this->config->auth->getDate(),
            Resource::AUTH_VERSION => Resource::VERSION,
        ]));

        if (!empty($body)) {
            $options['body'] = $body;

            if (!$headers->has('Content-Length')) {
                $headers->setContentLength(strlen($body));
            }
        }

        if ($this->shouldAuthenticate) {
            $headers = $headers->withAdditionalHeaders([
                Resource::AUTH_HEADER => $this->config->auth->getAuthentication($verb, $headers, $resource),
            ]);
        } else {
            $this->withAuthentication();
        }

        $options['headers'] = $headers->toArray();

        return $options;
    }
}
