<?php

use Xray\AzureStoragePhpSdk\BlobStorage\Concerns\ValidateMetadataKey;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

uses()->group('blob-storage', 'concerns');

it('should not throw an exception if the key is valid', function () {
    $class = new class () {
        use ValidateMetadataKey;

        public function assertMetadataKey(string $key): void
        {
            $this->validateMetadataKey($key);

            expect(true)->toBeTrue();
        }
    };

    $class->assertMetadataKey('key');
});

it('should throw an exception if the key starts with a number', function () {
    $class = new class () {
        use ValidateMetadataKey;

        public function assertMetadataKey(string $key): void
        {
            expect(fn () => $this->validateMetadataKey($key))
                ->toThrow(InvalidArgumentException::class, "Invalid metadata key: {$key}. Metadata keys cannot start with a number.");
        }
    };

    $class->assertMetadataKey('1key');
});

it('should throw an exception if the key contains invalid characters', function () {

    $class = new class () {
        use ValidateMetadataKey;

        public function assertMetadataKey(string $key): void
        {
            expect(fn () => $this->validateMetadataKey($key))
                ->toThrow(InvalidArgumentException::class, "Invalid metadata key: {$key}. Only alphanumeric characters and underscores are allowed.");
        }
    };

    $class->assertMetadataKey('-test-');
});
