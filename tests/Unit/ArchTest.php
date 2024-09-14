<?php

use Xray\AzureStoragePhpSdk\Contracts\Manager;

arch()->preset()->php();
arch()->preset()->security()->ignoring('md5');

arch('it should not use dumping functions')
    ->expect(['dd', 'dump', 'die', 'exit', 'var_dump', 'var_export'])
    ->not->toBeUsed();

arch('should use strict types everywhere')
    ->expect('Xray\\AzureStoragePhpSdk')
    ->toUseStrictTypes();

arch('it should all entities be final')
    ->expect('Xray\\AzureStoragePhpSdk\\BlobStorage\\Entities')
    ->classes()
    ->toBeFinal();

arch('it should all manger implements Manage interface')
    ->expect('Xray\\AzureStoragePhpSdk\\BlobStorage\\Managers')
    ->classes()
    ->toImplement(Manager::class);
