<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Tests\Http;

use Closure;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\{Request, Response};

class RequestFake implements Request
{
    /** @var array<string, scalar> */
    protected array $options = [];

    /** @var array<string, scalar> */
    protected array $headers = [];

    protected ?Closure $usingAccountCallback = null;

    protected bool $shouldAuthenticate = true;

    /** @var array<string, scalar> */
    protected array $methods = [
        'get'     => null,
        'post'    => null,
        'put'     => null,
        'delete'  => null,
        'options' => null,
    ];

    public function __construct(protected Config $config)
    {
        //
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

    public function get(string $endpoint): Response
    {
        $this->methods['get'] = [
            'endpoint' => $endpoint,
        ];

        return new ResponseFake();
    }

    public function post(string $endpoint, string $body = ''): Response
    {
        $this->methods['post'] = [
            'endpoint' => $endpoint,
            'body'     => $body,
        ];

        return new ResponseFake();
    }

    public function put(string $endpoint, string $body = ''): Response
    {
        $this->methods['put'] = [
            'endpoint' => $endpoint,
            'body'     => $body,
        ];

        return new ResponseFake();
    }

    public function delete(string $endpoint): Response
    {
        $this->methods['delete'] = [
            'endpoint' => $endpoint,
        ];

        return new ResponseFake();
    }

    public function options(string $endpoint): Response
    {
        $this->methods['options'] = [
            'endpoint' => $endpoint,
        ];

        return new ResponseFake();
    }
}
