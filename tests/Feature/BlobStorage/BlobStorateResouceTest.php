<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Resource;

pest()->group('blob-storage');
covers(Resource::class);

it('should canonicalize resource correctly', function (string $query, string $expected) {
    $uri = "https://account.blob.core.windows.net/container/?{$query}";

    expect(Resource::canonicalize($uri))
        ->toBe("/container/\n{$expected}");
})->with([
    'With Query Params'    => ['RestType=service&Comp=properties', "comp:properties\nresttype:service"],
    'Without Query Params' => ['', ''],
]);
