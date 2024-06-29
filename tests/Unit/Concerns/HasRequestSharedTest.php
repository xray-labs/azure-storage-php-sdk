<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Config;
use Sjpereira\AzureStoragePhpSdk\Concerns\HasRequestShared;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('concerns', 'traits');

it('should have a request shared property', function () {
    $request = new RequestFake(new Config(['account' => 'my_account', 'key' => 'bar']));

    $class = new class ($request) {
        /** @use HasRequestShared<RequestFake> */
        use HasRequestShared;

        public function __construct(protected Request $request)
        {
            //
        }
    };

    expect($class->getRequest())
        ->toBeInstanceOf(Request::class)
        ->toBe($request);
});
