<?php

declare(strict_types=1);

use GuzzleHttp\Exception\RequestException as GuzzleRequestException;
use GuzzleHttp\Psr7\{Request, Response};
use Xray\AzureStoragePhpSdk\Exceptions\RequestException;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException\FailedAuthenticationException;

pest()->group('exceptions');
covers(RequestException::class);

it('should be an exception', function () {
    expect(RequestException::class)
        ->toExtend(Exception::class);
});

it('should create a request exception from a guzzle request exception interface', function (int $statusCode, string $exceptionClass) {
    $guzzleException = new GuzzleRequestException(
        $message = 'Something went wrong',
        new Request('GET', 'http://example.com'),
        new Response($statusCode),
    );

    expect(RequestException::createFromRequestException($guzzleException))
        ->toBeInstanceOf($exceptionClass) // @phpstan-ignore-line
        ->getMessage()->toBe($message)
        ->getCode()->toBe($statusCode)
        ->getPrevious()->toBe($guzzleException);
})->with([
    'Bad Request'           => [400, RequestException::class],
    'Unauthorized'          => [401, RequestException::class],
    'Forbidden'             => [403, FailedAuthenticationException::class],
    'Not Found'             => [404, RequestException::class],
    'Method Not Allowed'    => [405, RequestException::class],
    'Request Timeout'       => [408, RequestException::class],
    'Conflict'              => [409, RequestException::class],
    'Unprocessable Entity'  => [422, RequestException::class],
    'Too Many Requests'     => [429, RequestException::class],
    'Internal Server Error' => [500, RequestException::class],
    'Bad Gateway'           => [502, RequestException::class],
    'Service Unavailable'   => [503, RequestException::class],
    'Gateway Timeout'       => [504, RequestException::class],
]);

it('should create a request exception from a message', function () {
    expect(RequestException::createFromMessage($message = 'Other error message'))
        ->toBeInstanceOf(RequestException::class)
        ->getMessage()->toBe($message);
});
