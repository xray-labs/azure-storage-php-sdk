<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Enums\BlobIncludeOption;

pest()->group('enums');
covers(BlobIncludeOption::class);

it('should return the enum as an array', function () {
    expect(BlobIncludeOption::toArray())
        ->toBeArray()
        ->toBe(array_map(fn (BlobIncludeOption $enum) => $enum->value, BlobIncludeOption::cases()));
});
