<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Tests\Http;

use Closure;
use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\Config;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\Http\{Request, Response};
use Xray\AzureStoragePhpSdk\Tests\Http\Concerns\{HasAuthAssertions, HasHttpAssertions};

/**
 * @phpstan-type Method array{endpoint: string, body?: string}
 */
class RequestFake implements Request
{
    use HasHttpAssertions;
    use HasAuthAssertions;

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
        $this->auth   = $auth ?? new SharedKeyAuth('account', 'key');
        $this->config = $config ?? new Config();
    }

    public function withFakeResponse(ResponseFake $fakeResponse): static
    {
        $this->fakeResponse = $fakeResponse;

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

        return $this;
    }

    public function get(string $endpoint): Response
    {
        $this->methods['get'] = [
            'endpoint' => $endpoint,
        ];

        return $this->fakeResponse ?? new ResponseFake();
    }

    public function post(string $endpoint, string $body = ''): Response
    {
        $this->methods['post'] = [
            'endpoint' => $endpoint,
            'body'     => $body,
        ];

        return $this->fakeResponse ?? new ResponseFake();
    }

    public function put(string $endpoint, string $body = ''): Response
    {
        $this->methods['put'] = [
            'endpoint' => $endpoint,
            'body'     => $body,
        ];

        return $this->fakeResponse ?? new ResponseFake();
    }

    public function delete(string $endpoint): Response
    {
        $this->methods['delete'] = [
            'endpoint' => $endpoint,
        ];

        return $this->fakeResponse ?? new ResponseFake();
    }

    public function options(string $endpoint): Response
    {
        $this->methods['options'] = [
            'endpoint' => $endpoint,
        ];

        return $this->fakeResponse ?? new ResponseFake();
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
