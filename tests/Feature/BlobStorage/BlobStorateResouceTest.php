<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Resource;

uses()->group('blob-storage');

it('should canonicalize resource correctly', function () {
    $uri = 'https://account.blob.core.windows.net/container/?RestType=service&Comp=properties';

    expect(Resource::canonicalize($uri))
        ->toBe("/container/\ncomp:properties\nresttype:service");
});
