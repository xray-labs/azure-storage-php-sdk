<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\InvalidFileMimeTypeException;

pest()->group('exceptions');
covers(InvalidFileMimeTypeException::class);

it('can create an InvalidFileMimeTypeException instance', function () {
    $exception = InvalidFileMimeTypeException::create();

    expect($exception)->toBeInstanceOf(InvalidFileMimeTypeException::class);
});

it('can create an InvalidFileMimeTypeException instance with a custom message', function () {
    $message   = 'Custom error message';
    $exception = InvalidFileMimeTypeException::create($message);

    expect($exception)->toBeInstanceOf(InvalidFileMimeTypeException::class);
    expect($exception->getMessage())->toBe($message);
});
