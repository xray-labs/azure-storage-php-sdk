<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums\HttpVerb;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Account\PreflightBlobRequestManager;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Tests\Http\RequestFake;

uses()->group('blob-storage', 'managers', 'account');

it('should send a request to the preflight blob', function (string $method, HttpVerb $verb) {
    $request = new RequestFake(new Config(new SharedKeyAuth('account', 'key')));
    $origin  = 'http://example.com';

    (new PreflightBlobRequestManager($request))
        ->{$method}($origin, $headers = ['key' => 'value']);

    $request->assertDelete('')
        ->assertWithoutAuthentication()
        ->assertSentWithHeaders([
            Resource::ORIGIN                         => $origin,
            Resource::ACCESS_CONTROL_REQUEST_METHOD  => $verb->value,
            Resource::ACCESS_CONTROL_REQUEST_HEADERS => implode(',', $headers),
        ]);
})->with([
    'Delete'  => ['delete', HttpVerb::DELETE],
    'Get'     => ['get', HttpVerb::GET],
    'Head'    => ['head', HttpVerb::HEAD],
    'Merge'   => ['merge', HttpVerb::MERGE],
    'Post'    => ['post', HttpVerb::POST],
    'Options' => ['options', HttpVerb::OPTIONS],
    'Put'     => ['put', HttpVerb::PUT],
    'Patch'   => ['patch', HttpVerb::PATCH],
]);
