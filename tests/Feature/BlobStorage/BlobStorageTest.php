<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\BlobStorageClient;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Xray\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('blob-storage');

it('should be able to get blob storage managers', function (string $method, string $class, array $parameters = []) {
    $request = new RequestFake();

    expect(new BlobStorageClient($request))
        ->{$method}(...$parameters)->toBeInstanceOf($class);
})->with([
    'Account Manager'   => ['account', AccountManager::class],
    'Container Manager' => ['containers', ContainerManager::class],
    'Blob Manager'      => ['blobs', BlobManager::class, ['test']],
]);

it('should create a new blob storage client', function () {
    $auth = new SharedKeyAuth('account', 'key');

    expect(BlobStorageClient::create($auth))
        ->toBeInstanceOf(BlobStorageClient::class);
});
