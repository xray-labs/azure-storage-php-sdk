<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\UnableToParseException;

pest()->group('exceptions');
covers(UnableToParseException::class);

it('should be an exception', function () {
    expect(UnableToParseException::class)
        ->toExtend(Exception::class);
});

it('should be an unable to parse exception', function () {
    expect(UnableToParseException::create('Can not parse'))
        ->getMessage()->toBe('Can not parse')
        ->getCode()->toBe(0);
});
