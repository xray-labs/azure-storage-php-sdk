<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{BlobStorage, Config};
use Sjpereira\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('blob-storage');

it('should be able to create a new client', function () {
    $client = BlobStorage::client(['account' => 'account', 'key' => 'key']);

    expect($client)->toBeInstanceOf(BlobStorage::class);
});

it('should be able to get blob storage managers', function (string $method, string $class, array $parameters = []) {
    $request = new RequestFake(new Config(['account' => 'account', 'key' => 'key']));

    expect(new BlobStorage($request))
        ->{$method}(...$parameters)->toBeInstanceOf($class);
})->with([
    'Account Manager'   => ['account', AccountManager::class],
    'Container Manager' => ['containers', ContainerManager::class],
    'Blob Manager'      => ['blobs', BlobManager::class, ['test']],
]);
