<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Exceptions\ManagerNotSetException;

pest()->group('exceptions');
covers(ManagerNotSetException::class);

it('should be an exception', function () {
    expect(ManagerNotSetException::class)
        ->toExtend(Exception::class);
});

it('should create a manager not set exception', function () {
    expect(ManagerNotSetException::create())
        ->getMessage()->toBe('Manager not set')
        ->getCode()->toBe(0);
});
