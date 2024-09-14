<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\InvalidResourceTypeException;

pest()->group('exceptions');
covers(InvalidResourceTypeException::class);

it('should be an exception', function () {
    expect(InvalidResourceTypeException::class)
        ->toExtend(Exception::class);
});

it('should create an InvalidResourceTypeException instance', function () {
    $exception = InvalidResourceTypeException::create($message = 'Invalid resource type');

    expect($exception)->toBeInstanceOf(InvalidResourceTypeException::class)
        ->getMessage()->toBe($message);
});
