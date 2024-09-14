<?php

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\BlobLease;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobLeaseManager;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

pest()->group('blob-storage', 'managers', 'blob');
covers(BlobLeaseManager::class);

const BLOB_LEASE_MANAGER_REQUEST_URL = 'container/blob?comp=lease&resttype=blob';
const BLOB_LEASE_MANAGER_VERSION     = '2020-06-12';
const BLOB_LEASE_MANAGER_LEASE_ID    = '29389-29389-398439';
const BLOB_LEASE_MANAGER_REQUEST_ID  = '923-2324-2134';

it('should acquire a lease', function () {
    ['request' => $request, 'manager' => $manager] = prepareForBlobLeaseManagerTest();

    $response = $manager->acquire();

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('ETAG_CODE')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe(BLOB_LEASE_MANAGER_REQUEST_ID)
        ->version->toBe(BLOB_LEASE_MANAGER_VERSION)
        ->leaseId->toBe(BLOB_LEASE_MANAGER_LEASE_ID)
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(BLOB_LEASE_MANAGER_REQUEST_URL);
});

it('should renew a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->renew();

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('ETAG_CODE')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe(BLOB_LEASE_MANAGER_REQUEST_ID)
        ->version->toBe(BLOB_LEASE_MANAGER_VERSION)
        ->leaseId->toBe(BLOB_LEASE_MANAGER_LEASE_ID)
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(BLOB_LEASE_MANAGER_REQUEST_URL);
});

it('should change a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->change(BLOB_LEASE_MANAGER_REQUEST_ID);

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('ETAG_CODE')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe(BLOB_LEASE_MANAGER_REQUEST_ID)
        ->version->toBe(BLOB_LEASE_MANAGER_VERSION)
        ->leaseId->toBe(BLOB_LEASE_MANAGER_LEASE_ID)
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(BLOB_LEASE_MANAGER_REQUEST_URL);
});

it('should release a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->release(BLOB_LEASE_MANAGER_REQUEST_ID);

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('ETAG_CODE')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe(BLOB_LEASE_MANAGER_REQUEST_ID)
        ->version->toBe(BLOB_LEASE_MANAGER_VERSION)
        ->leaseId->toBe(BLOB_LEASE_MANAGER_LEASE_ID)
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(BLOB_LEASE_MANAGER_REQUEST_URL);
});

it('should break a lease', function () {
    ['request' => $request, 'blobLease' => $blobLease] = prepareForBlobLeaseManagerTest();

    $response = $blobLease->break(BLOB_LEASE_MANAGER_REQUEST_ID);

    expect($response)
        ->lastModified->toBeInstanceOf(DateTimeImmutable::class)
        ->etag->toBe('ETAG_CODE')
        ->server->toBe('MS-AZURE')
        ->requestId->toBe(BLOB_LEASE_MANAGER_REQUEST_ID)
        ->version->toBe(BLOB_LEASE_MANAGER_VERSION)
        ->leaseId->toBe(BLOB_LEASE_MANAGER_LEASE_ID)
        ->date->toBeInstanceOf(DateTimeImmutable::class);

    $request->assertPut(BLOB_LEASE_MANAGER_REQUEST_URL);
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
        'ETag'            => 'ETAG_CODE',
        'Server'          => 'MS-AZURE',
        'Date'            => 'Wed, 15 Sep 2021 15:02:29 GMT',
        'x-ms-request-id' => BLOB_LEASE_MANAGER_REQUEST_ID,
        'x-ms-version'    => BLOB_LEASE_MANAGER_VERSION,
        'x-ms-lease-id'   => BLOB_LEASE_MANAGER_LEASE_ID,
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
