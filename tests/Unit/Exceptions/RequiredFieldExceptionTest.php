<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

uses()->group('exceptions');

it('should be an exception', function () {
    expect(RequiredFieldException::class)
        ->toExtend(Exception::class);
});

it('should be a required field exception', function () {
    expect(RequiredFieldException::missingField('name'))
        ->getMessage()->toBe('Field [name] is required')
        ->getCode()->toBe(0);
});
