<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\{AccountManager, ContainerManager};
use Xray\AzureStoragePhpSdk\BlobStorage\{BlobStorage, Config};
use Xray\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('blob-storage');

it('should be able to get blob storage managers', function (string $method, string $class, array $parameters = []) {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));

    expect(new BlobStorage($request))
        ->{$method}(...$parameters)->toBeInstanceOf($class);
})->with([
    'Account Manager'   => ['account', AccountManager::class],
    'Container Manager' => ['containers', ContainerManager::class],
    'Blob Manager'      => ['blobs', BlobManager::class, ['test']],
]);
