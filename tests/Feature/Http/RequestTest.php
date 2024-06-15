<?php

declare(strict_types=1);

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Promise\{Promise, PromiseInterface};
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Assert;
use Psr\Http\Message\{RequestInterface, ResponseInterface};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Response as HttpResponse;
use Sjpereira\AzureStoragePhpSdk\Http\Request;

uses()->group('http');

it('should send get, delete, and options requests', function (string $method, HttpVerb $verb): void {
    $config = new Config(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($client = new Client(), $config))
        ->withAuthentication()
        ->usingAccount(fn (): string => 'foo')
        ->withOptions(['foo' => 'bar']);

    expect($request->{$method}('endpoint'))
        ->toBeInstanceOf(HttpResponse::class);

    $client->assertRequestSent(
        $verb->value,
        'https://foo.blob.core.windows.net/endpoint',
        fn (array $options): bool => $options['foo'] === 'bar'
            && array_key_exists(Resource::AUTH_DATE_KEY, $options['headers'])
            && array_key_exists(Resource::AUTH_HEADER_KEY, $options['headers'])
            && array_key_exists(Resource::AUTH_VERSION_KEY, $options['headers'])
    );
})->with([
    'With GET method'     => ['get', HttpVerb::GET],
    'With DELETE method'  => ['delete', HttpVerb::DELETE],
    'With OPTIONS method' => ['options', HttpVerb::OPTIONS],
]);

it('should send post and put requests', function (string $method, HttpVerb $verb): void {
    $config = new Config(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($client = new Client(), $config))
        ->withoutAuthentication()
        ->withHeaders(['foo' => 'bar']);

    $body = <<<XML
    <?xml version="1.0" encoding="UTF-8"?>
    <title>Test</title>
    XML;

    expect($request->{$method}('endpoint', $body))
        ->toBeInstanceOf(HttpResponse::class);

    $client->assertRequestSent(
        $verb->value,
        'https://my_account.blob.core.windows.net/endpoint',
        fn (array $options): bool => $options['headers']['foo'] === 'bar'
            && $options['body'] === $body
            && array_key_exists(Resource::AUTH_DATE_KEY, $options['headers'])
            && !array_key_exists(Resource::AUTH_HEADER_KEY, $options['headers'])
            && array_key_exists(Resource::AUTH_VERSION_KEY, $options['headers'])
    );
})->with([
    'With PUT method'  => ['put', HttpVerb::PUT],
    'With POST method' => ['post', HttpVerb::POST],
]);

it('should get request config', function (): void {
    $config = new Config(['account' => 'my_account', 'key' => 'bar']);

    expect((new Request(new Client(), $config))->getConfig())
        ->toBe($config);
});

class Client implements ClientInterface
{
    /** @var array<string, array{uri: string, options: array<string, scalar>}> */
    protected array $requests = [];

    /** @param array<string, scalar> $options */
    public function send(RequestInterface $request, array $options = []): ResponseInterface
    {
        return new Response();
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

        return new Response();
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
