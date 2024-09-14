<?php

declare(strict_types=1);

use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Xray\AzureStoragePhpSdk\Http\Response;

pest()->group('http');
covers(Response::class);

it('should create from guzzle response', function (): void {
    expect(Response::createFromGuzzleResponse(new GuzzleResponse()))
        ->toBeInstanceOf(Response::class);
});

it('should get headers', function (): void {
    $response = new Response(new GuzzleResponse(200, ['Content-Type' => 'application/json']));
    expect($response->getHeaders())->toBe(['Content-Type' => 'application/json']);
});

it('should get header line', function (): void {
    $response = new Response(new GuzzleResponse(200, ['Content-Type' => ['application/json']]));
    expect($response->getHeaderLine('Content-Type'))->toBe(['application/json']);
});

it('should assert status code', function (int $statusCode, string $function): void {
    $response = new Response(new GuzzleResponse($statusCode));
    expect($response)
        ->$function()->toBeTrue()
        ->getStatusCode()->toBe($statusCode);
})->with([
    'OK'         => [200, 'isOk'],
    'Created'    => [201, 'isCreated'],
    'Accepted'   => [202, 'isAccepted'],
    'No Content' => [204, 'isNoContent'],
]);

it('should get body', function (): void {
    $response = new Response(new GuzzleResponse(200, [], 'body'));
    expect($response->getBody())->toBe('body');
});

it('should validate expiration time when streaming or downloading', function (string $method) {
    $file = new File('file', 'file.txt', ['Content-Type' => 'text/plain']);

    Response::{$method}($file, -1);
})->with([
    'Streaming'   => ['stream'],
    'Downloading' => ['download'],
])->throws(InvalidArgumentException::class, 'Expires cannot be less than 0.');

it('should stream or download the file through the response', function (string $method) {
    $file = new File('file.txt', $content = 'content', ['Content-Type' => 'text/plain']);

    expect(Response::{$method}($file))->toBe($content);
})->with([
    'Streaming'   => ['stream'],
    'Downloading' => ['download'],
]);
