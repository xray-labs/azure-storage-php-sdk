<?php

use Xray\AzureStoragePhpSdk\BlobStorage\Concerns\ValidateContainerName;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

pest()->group('blob-storage', 'concerns');
covers(ValidateContainerName::class);

it('should not throw an exception if the container name is valid', function () {
    $class = new class () {
        use ValidateContainerName;

        public function assertContainerName(string $name): void
        {
            $this->validateContainerName($name);

            expect(true)->toBeTrue();
        }
    };

    $class->assertContainerName('test');
});

it('should throw an exception if the container name is invalid', function (string $containerName) {
    $class = new class () {
        use ValidateContainerName;

        public function assertContainerName(string $name): void
        {
            expect(fn () => $this->validateContainerName($name))
                ->toThrow(InvalidArgumentException::class, "Invalid container name: {$name}");
        }
    };

    $class->assertContainerName($containerName);
})->with([
    'With Capital Letters' => ['TEST'],
    'With Spaces'          => ['test test'],
    'With Special Chars'   => ['test*'],
]);
