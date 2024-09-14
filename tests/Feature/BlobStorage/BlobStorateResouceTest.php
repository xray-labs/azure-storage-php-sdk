<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Resource;

pest()->group('blob-storage');
covers(Resource::class);

it('should canonicalize resource correctly', function () {
    $uri = 'https://account.blob.core.windows.net/container/?RestType=service&Comp=properties';

    expect(Resource::canonicalize($uri))
        ->toBe("/container/\ncomp:properties\nresttype:service");
});
