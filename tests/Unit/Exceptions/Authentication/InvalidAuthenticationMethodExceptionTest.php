<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\Authentication\InvalidAuthenticationMethodException;

pest()->group('exceptions', 'authentications');
covers(InvalidAuthenticationMethodException::class);

it('should be an exception', function () {
    expect(InvalidAuthenticationMethodException::class)
        ->toExtend(Exception::class);
});

it('should create an InvalidAuthenticationMethodException instance', function () {
    $exception = InvalidAuthenticationMethodException::create($message = 'Invalid authentication method');

    expect($exception)->toBeInstanceOf(InvalidAuthenticationMethodException::class)
        ->getMessage()->toBe($message);
});
