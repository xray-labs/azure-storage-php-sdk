<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Http;

use Closure;
use GuzzleHttp\{Client, ClientInterface};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Xray\AzureStoragePhpSdk\Concerns\Http\{HasAuthenticatedRequest, HasSharingMethods};
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\{Request as RequestContract, Response as ResponseContract};

/** @suppressWarnings(PHPMD.ExcessiveParameterList) */
class Request implements RequestContract
{
    use HasAuthenticatedRequest;
    use HasSharingMethods;

    protected readonly ClientInterface $client;

    protected readonly Config $config;

    protected readonly string $protocol;

    protected readonly string $domain;

    /** @var array<string, scalar> */
    protected array $options = [];

    /** @var array<string, scalar> */
    protected array $headers = [];

    public function __construct(
        protected readonly Auth $auth,
        ?Config $config = null,
        ?ClientInterface $client = null,
        ?string $protocol = null,
        ?string $domain = null,
    ) {
        validate_protocol($protocol ??= 'https');

        $this->client   = $client ?? azure_app(Client::class);
        $this->config   = $config ?? azure_app(Config::class);
        $this->protocol = $protocol;
        $this->domain   = $domain ?? 'blob.core.windows.net';
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
        $account = $this->auth->getAccount();

        if (!is_null($this->usingAccountCallback)) {
            $account = call_user_func($this->usingAccountCallback, $account);

            $this->usingAccountCallback = null;
        }

        if (!is_null($endpoint)) {
            [$endpoint, $params] = array_pad(explode('?', $endpoint, 2), 2, '');

            $endpoint = implode('/', array_map('rawurlencode', explode('/', $endpoint))) . "?{$params}";
        }

        return "{$this->protocol}://{$account}.{$this->domain}/{$endpoint}";
    }

    /** @return array<string, mixed> */
    protected function getOptions(HttpVerb $verb, string $resource, string $body = ''): array
    {
        $this->withVerb($verb)
            ->withResource($resource)
            ->withBody($body);

        $options = $this->options;

        $headers = Headers::parse(array_merge($this->headers, [
            Resource::AUTH_DATE    => $this->auth->getDate(),
            Resource::AUTH_VERSION => Resource::VERSION,
        ]));

        if (!empty($body)) {
            $options['body'] = $body;

            if (!$headers->has(Resource::CONTENT_LENGTH)) {
                $headers->setContentLength(strlen($body));
            }
        }

        if ($this->shouldAuthenticate) {
            $headers = $headers->withAdditionalHeaders([
                Resource::AUTH_HEADER => $this->auth->getAuthentication($this->withHttpHeaders($headers)),
            ]);
        }

        $options['headers'] = $headers->toArray();

        return with($options, fn () => $this->resetRequestOptions());
    }

    protected function resetRequestOptions(): void
    {
        $this->headers     = [];
        $this->options     = [];
        $this->verb        = HttpVerb::GET;
        $this->httpHeaders = new Headers();
        $this->resource    = '';
        $this->body        = '';

        $this->withAuthentication();
        azure_app()->flushScoped();
    }
}
