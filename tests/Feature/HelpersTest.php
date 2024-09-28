<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Application\Application;

pest()->group('helpers');

it('should get the azure_app instance', function () {
    expect(azure_app())
        ->toBeInstanceOf(Application::class);
})->covers('azure_app');
