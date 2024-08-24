<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Tests\Http;

use Closure;
use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\Config;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\{Request, Response};
use Xray\AzureStoragePhpSdk\Http\Headers;
use Xray\AzureStoragePhpSdk\Tests\Http\Concerns\{HasAuthAssertions, HasHttpAssertions, HasSharableHttp};

/**
 * @phpstan-type Method array{endpoint: string, body?: string}
 */
class RequestFake implements Request
{
    use HasHttpAssertions;
    use HasAuthAssertions;
    use HasSharableHttp;

    protected readonly Auth $auth;

    protected readonly Config $config;

    /** @var array<string, scalar> */
    protected array $options = [];

    /** @var array<string, scalar> */
    protected array $headers = [];

    protected ?Closure $usingAccountCallback = null;

    protected bool $shouldAuthenticate = true;

    /** @var array{get: ?Method, post: ?Method, put: ?Method, delete: ?Method, options: ?Method} */
    protected array $methods = [
        'get'     => null,
        'post'    => null,
        'put'     => null,
        'delete'  => null,
        'options' => null,
    ];

    protected ?ResponseFake $fakeResponse = null;

    public function __construct(?Auth $auth = null, ?Config $config = null)
    {
        $this->auth   = $auth ?? azure_app(SharedKeyAuth::class, ['config' => ['account' => 'account', 'key' => 'key']]);
        $this->config = $config ?? azure_app(Config::class);
    }

    public function withFakeResponse(ResponseFake $fakeResponse): static
    {
        $this->fakeResponse = $fakeResponse;

        azure_app()->instance(Request::class, $this);

        return $this;
    }

    public function usingAccount(Closure $callback): static
    {
        $this->usingAccountCallback = $callback;

        return $this;
    }

    public function getAuth(): Auth
    {
        return $this->auth;
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
        $this->withHttpHeaders(Headers::parse($this->headers));

        return $this;
    }

    public function get(string $endpoint): Response
    {
        $this->methods['get'] = [
            'endpoint' => $endpoint,
        ];

        $this->withVerb(HttpVerb::GET);

        return $this->fakeResponse ?? azure_app(ResponseFake::class);
    }

    public function post(string $endpoint, string $body = ''): Response
    {
        $this->methods['post'] = [
            'endpoint' => $endpoint,
            'body'     => $body,
        ];

        $this->withVerb(HttpVerb::POST)
            ->withBody($body);

        return $this->fakeResponse ?? azure_app(ResponseFake::class);
    }

    public function put(string $endpoint, string $body = ''): Response
    {
        $this->methods['put'] = [
            'endpoint' => $endpoint,
            'body'     => $body,
        ];

        $this->withVerb(HttpVerb::PUT)
            ->withBody($body);

        return $this->fakeResponse ?? azure_app(ResponseFake::class);
    }

    public function delete(string $endpoint): Response
    {
        $this->methods['delete'] = [
            'endpoint' => $endpoint,
        ];

        $this->withVerb(HttpVerb::DELETE);

        return $this->fakeResponse ?? azure_app(ResponseFake::class);
    }

    public function options(string $endpoint): Response
    {
        $this->methods['options'] = [
            'endpoint' => $endpoint,
        ];

        $this->withVerb(HttpVerb::OPTIONS);

        return $this->fakeResponse ?? azure_app(ResponseFake::class);
    }

    public function uri(?string $endpoint = null): string
    {
        $account = $this->auth->getAccount();

        if (!is_null($endpoint)) {
            [$endpoint, $params] = array_pad(explode('?', $endpoint, 2), 2, '');

            $endpoint = implode('/', array_map('rawurlencode', explode('/', $endpoint))) . "?{$params}";
        }

        return "http://{$account}.microsoft.azure/{$endpoint}";
    }
}
