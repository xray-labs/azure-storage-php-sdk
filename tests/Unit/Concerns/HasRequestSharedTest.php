<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Concerns\HasRequestShared;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('concerns', 'traits');

it('should have a request shared property', function () {
    $request = new RequestFake();
    $class   = new class ($request) {
        /** @use HasRequestShared<RequestFake> */
        use HasRequestShared;

        public function __construct(protected RequestFake $request)
        {
            //
        }
    };

    expect($class->getRequest())
        ->toBeInstanceOf(Request::class)
        ->toBe($request);
});
