<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

pest()->group('exceptions');
covers(UnableToConvertException::class);

it('should be an exception', function () {
    expect(UnableToConvertException::class)
        ->toExtend(Exception::class);
});

it('should be an unable to convert exception', function () {
    expect(UnableToConvertException::create('Can not convert'))
        ->getMessage()->toBe('Can not convert')
        ->getCode()->toBe(0);
});
