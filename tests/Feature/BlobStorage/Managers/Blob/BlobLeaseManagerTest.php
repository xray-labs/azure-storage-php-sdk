<?php

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobLease;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobLeaseManager;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'blob');

const REQUEST_URL = 'container/blob?comp=lease&resttype=blob';
const VERSION     = '2020-06-12';

it('should acquire a lease', function () {
    ['request' => $request, 'manager' => $manager] = prepareForBlobLeaseManagerTest();

    $response = $manager->acquire();

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('KCSNSAKDMA')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe('osjdnw-29389dksd-dwwdwd')
        ->version->toBe(VERSION)
        ->leaseId->toBe('lalsncjwej-29389dksd-dwwdwd')
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(REQUEST_URL);
});

it('should renew a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->renew();

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('KCSNSAKDMA')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe('osjdnw-29389dksd-dwwdwd')
        ->version->toBe(VERSION)
        ->leaseId->toBe('lalsncjwej-29389dksd-dwwdwd')
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(REQUEST_URL);
});

it('should change a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->change('lalsncjwej-29389dksd-dwwdwd');

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('KCSNSAKDMA')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe('osjdnw-29389dksd-dwwdwd')
        ->version->toBe(VERSION)
        ->leaseId->toBe('lalsncjwej-29389dksd-dwwdwd')
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(REQUEST_URL);
});

it('should release a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->release('lalsncjwej-29389dksd-dwwdwd');

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('KCSNSAKDMA')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe('osjdnw-29389dksd-dwwdwd')
        ->version->toBe(VERSION)
        ->leaseId->toBe('lalsncjwej-29389dksd-dwwdwd')
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(REQUEST_URL);
});

it('should break a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->break('lalsncjwej-29389dksd-dwwdwd');

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('KCSNSAKDMA')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe('osjdnw-29389dksd-dwwdwd')
        ->version->toBe(VERSION)
        ->leaseId->toBe('lalsncjwej-29389dksd-dwwdwd')
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(REQUEST_URL);
});

it('should throw an exception when trying to renew a lease without a lease id', function () {
    ['blobLease' => $blobLease] = prepareForBlobLeaseManagerTest(['x-ms-lease-id' => '']);

    $blobLease->renew();
})->throws(RequiredFieldException::class, 'Field [leaseId] is required');

/**
 * @param array<string, scalar> $blobLeaseHeaders
 * @return array{request: RequestFake, blobLease: BlobLease, manager: BlobLeaseManager}
 */
function prepareForBlobLeaseManagerTest(array $blobLeaseHeaders = []): array
{
    $blobLeaseHeaders = array_merge([
        'Last-Modified'   => 'Wed, 15 Sep 2021 15:02:29 GMT',
        'ETag'            => 'KCSNSAKDMA',
        'Server'          => 'MS-AZURE',
        'Date'            => 'Wed, 15 Sep 2021 15:02:29 GMT',
        'x-ms-request-id' => 'osjdnw-29389dksd-dwwdwd',
        'x-ms-version'    => VERSION,
        'x-ms-lease-id'   => 'lalsncjwej-29389dksd-dwwdwd',
    ], $blobLeaseHeaders);

    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: $blobLeaseHeaders));

    // @phpstan-ignore-next-line
    $blobLease = new BlobLease($blobLeaseHeaders);

    $manager = (new BlobLeaseManager($request, 'container', 'blob'));

    $blobLease->setManager($manager);

    return [
        'request'   => $request,
        'blobLease' => $blobLease,
        'manager'   => $manager,
    ];
}
