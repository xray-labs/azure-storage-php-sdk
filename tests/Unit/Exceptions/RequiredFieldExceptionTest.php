<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

pest()->group('exceptions');
covers(RequiredFieldException::class);

it('should be an exception', function () {
    expect(RequiredFieldException::class)
        ->toExtend(Exception::class);
});

it('should create a new required field exception', function () {
    expect(RequiredFieldException::create('This is a new required field exception'))
        ->getMessage()->toBe('This is a new required field exception')
        ->getCode()->toBe(0);
});

it('should be a required field exception', function () {
    expect(RequiredFieldException::missingField('name'))
        ->getMessage()->toBe('Field [name] is required')
        ->getCode()->toBe(0);
});
