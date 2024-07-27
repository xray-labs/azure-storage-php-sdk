<?php

declare(strict_types=1);

namespace Xray\Tests\Fakes;

use Closure;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\{Promise, PromiseInterface};
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\{RequestInterface, ResponseInterface};

class ClientFake implements ClientInterface
{
    /** @var array<string, array{uri: string, options: array<string, scalar>}> */
    protected array $requests = [];

    protected int $status = 200;

    protected array $headers = [];

    protected ?string $body = null;

    public function withResponseFake(?string $body = null, array $headers = [], int $status = 200): self
    {
        $this->status  = $status;
        $this->headers = $headers;
        $this->body    = $body;

        return $this;
    }

    /** @param array<string, scalar> $options */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return new Response($this->status, $this->headers, $this->body);
    }

    /** @param array<string, scalar> $options */
    public function sendAsync(RequestInterface $request, array $options = []): PromiseInterface
    {
        return new Promise();
    }

    /** @param array<string, scalar> $options */
    public function request(string $method, mixed $uri, array $options = []): ResponseInterface
    {
        /** @phpstan-ignore-next-line */
        $this->requests[$method] = [
            'uri'     => $uri,
            'options' => $options,
        ];

        return new Response($this->status, $this->headers, $this->body);
    }

    /** @param array<string, scalar> $options */
    public function requestAsync(string $method, mixed $uri, array $options = []): PromiseInterface
    {
        return new Promise();
    }

    public function getConfig(?string $option = null): mixed
    {
        return [];
    }

    public function assertRequestSent(string $method, string $uri, ?Closure $options = null): void
    {
        Assert::assertArrayHasKey($method, $this->requests, 'Request not sent');
        Assert::assertSame($uri, $this->requests[$method]['uri'], 'Invalid URI');

        if (!is_null($options)) {
            Assert::assertTrue($options($this->requests[$method]['options']), 'Invalid options');
        }
    }
}
