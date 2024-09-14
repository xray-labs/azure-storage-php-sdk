<?php

use Xray\AzureStoragePhpSdk\BlobStorage\Concerns\HasFakeRequest;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request as RequestContract;
use Xray\AzureStoragePhpSdk\Fakes\Http\RequestFake;

pest()->group('blob-storage', 'concerns');
covers(HasFakeRequest::class);

it('should create a fake instance', function () {
    $class = ClientToHasFakeRequestTest::fake();

    expect($class)
        ->toBeInstanceOf(ClientToHasFakeRequestTest::class)
        ->request->toBeInstanceOf(RequestFake::class);
});

readonly class ClientToHasFakeRequestTest
{
    use HasFakeRequest;

    public function __construct(public RequestContract $request)
    {
        //
    }
}
