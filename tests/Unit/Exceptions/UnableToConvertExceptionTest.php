<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

uses()->group('exceptions');

it('should be an exception', function () {
    expect(UnableToConvertException::class)
        ->toExtend(Exception::class);
});

it('should be an unable to convert exception', function () {
    expect(UnableToConvertException::create('Can not convert'))
        ->getMessage()->toBe('Can not convert')
        ->getCode()->toBe(0);
});
