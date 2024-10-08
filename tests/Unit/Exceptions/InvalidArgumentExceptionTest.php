<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

pest()->group('exceptions');
covers(InvalidArgumentException::class);

it('should be an exception', function () {
    expect(InvalidArgumentException::class)
        ->toExtend(Exception::class);
});

it('should create an invalid argument exception', function () {
    expect(InvalidArgumentException::create('Invalid argument'))
        ->getMessage()->toBe('Invalid argument')
        ->getCode()->toBe(0);
});
