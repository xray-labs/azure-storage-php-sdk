<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Xray\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Xray\AzureStoragePhpSdk\Contracts\Http\Response as HttpResponse;
use Xray\AzureStoragePhpSdk\Http\{Headers, Request};
use Xray\Tests\Fakes\ClientFake;

uses()->group('http');

it('should send get, delete, and options requests', function (string $method, HttpVerb $verb): void {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($auth, client: $client = new ClientFake()))
        ->withAuthentication()
        ->usingAccount(fn (): string => 'foo')
        ->withOptions(['foo' => 'bar']);

    expect($request->{$method}('endpoint'))
        ->toBeInstanceOf(HttpResponse::class);

    $client->assertRequestSent(
        $verb->value,
        'https://foo.blob.core.windows.net/endpoint?',
        fn (array $options): bool => $options['foo'] === 'bar'
            && array_key_exists(Resource::AUTH_DATE, $options['headers'])
            && array_key_exists(Resource::AUTH_HEADER, $options['headers'])
            && array_key_exists(Resource::AUTH_VERSION, $options['headers'])
    );

    $getRequestOptions = fn () => (object)[
        'options' => $this->options,
        'headers' => $this->headers,
    ];

    expect($getRequestOptions->call($request))
        ->options->toBeEmpty()
        ->headers->toBeEmpty();
})->with([
    'With GET method'     => ['get', HttpVerb::GET],
    'With DELETE method'  => ['delete', HttpVerb::DELETE],
    'With OPTIONS method' => ['options', HttpVerb::OPTIONS],
]);

it('should send post and put requests', function (string $method, HttpVerb $verb): void {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($auth, client: $client = new ClientFake()))
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
        'https://my_account.blob.core.windows.net/endpoint?',
        fn (array $options): bool => $options['headers']['foo'] === 'bar'
            && $options['body'] === $body
            && array_key_exists(Resource::AUTH_DATE, $options['headers'])
            && !array_key_exists(Resource::AUTH_HEADER, $options['headers'])
            && array_key_exists(Resource::AUTH_VERSION, $options['headers'])
    );

    $getRequestOptions = fn () => (object)[
        'options'            => $this->options,
        'headers'            => $this->headers,
        'shouldAuthenticate' => $this->shouldAuthenticate,
    ];

    expect($getRequestOptions->call($request))
        ->options->toBeEmpty()
        ->headers->toBeEmpty()
        ->shouldAuthenticate->toBeTrue();
})->with([
    'With PUT method'  => ['put', HttpVerb::PUT],
    'With POST method' => ['post', HttpVerb::POST],
]);

it('should get request config', function (): void {
    $auth   = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);
    $config = new Config();

    expect((new Request($auth, $config, new ClientFake()))->getConfig())
        ->toBe($config);
});

it('should get request auth', function (): void {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    expect((new Request($auth, client: new ClientFake()))->getAuth())
        ->toBe($auth);
});

it('should get the http verb from request', function (HttpVerb $verb) {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($auth, client: new ClientFake()))
        ->withVerb($verb);

    expect($request->getVerb())
        ->toBeInstanceOf(HttpVerb::class)
        ->toEqual($verb);
})->with(fn () => HttpVerb::cases());

it('should get the resource from request', function (): void {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($auth, client: new ClientFake()))
        ->withResource('endpoint');

    expect($request->getResource())
        ->toEqual('endpoint');
});

it('should get the headers from request', function (): void {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($auth, client: new ClientFake()))
        ->withHttpHeaders(new Headers());

    expect($request->getHttpHeaders())
        ->toBeInstanceOf(Headers::class);
});

it('should get the body from request', function (): void {
    $auth = new SharedKeyAuth(['account' => 'my_account', 'key' => 'bar']);

    $request = (new Request($auth, client: new ClientFake()))
        ->withBody('body');

    expect($request->getBody())
        ->toBe('body');
});
