<?php

use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;

arch('it should not use dumping functions')
    ->expect(['dd', 'dump', 'die', 'exit', 'var_dump', 'var_export'])
    ->not->toBeUsed();

arch('should use strict types everywhere')
    ->expect('Sjpereira\\AzureStoragePhpSdk')
    ->toUseStrictTypes();

arch('it should all entities be final')
    ->expect('Sjpereira\\AzureStoragePhpSdk\\BlobStorage\\Entities')
    ->classes()
    ->toBeFinal();

arch('it should all manger implements Manage interface')
    ->expect('Sjpereira\\AzureStoragePhpSdk\\BlobStorage\\Managers')
    ->classes()
    ->toImplement(Manager::class);
