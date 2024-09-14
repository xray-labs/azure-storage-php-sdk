<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\CouldNotCreateTempFileException;

pest()->group('exceptions');
covers(CouldNotCreateTempFileException::class);

it('should be an exception', function () {
    expect(CouldNotCreateTempFileException::class)
        ->toExtend(Exception::class);
});

it('should create an CouldNotCreateTempFileException instance', function () {
    $exception = CouldNotCreateTempFileException::create($message = 'Could not create temporary file');

    expect($exception)->toBeInstanceOf(CouldNotCreateTempFileException::class)
        ->getMessage()->toBe($message);
});
