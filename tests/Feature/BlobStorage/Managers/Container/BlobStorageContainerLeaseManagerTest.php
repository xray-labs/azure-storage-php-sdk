<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerLease;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerLeaseManager;
use Xray\AzureStoragePhpSdk\BlobStorage\{Resource};
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'containers');

it('should acquire a new lease', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'   => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'            => ['etag'],
            'Server'          => ['server'],
            'x-ms-request-id' => ['request-id'],
            'x-ms-version'    => ['version'],
            'x-ms-lease-id'   => ['lease-id'],
            'Date'            => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerLeaseManager($request, $container = 'container');

    expect($manager->acquire($duration = 10, $leaseId = 'leaseId'))
        ->toBeInstanceOf(ContainerLease::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2024-06-10T00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->requestId->toBe('request-id')
        ->version->toBe('version')
        ->date->format('Y-m-d\TH:i:s')->toBe('2024-06-11T00:00:00')
        ->leaseId->toBe('lease-id');

    $request->assertPut("{$container}?comp=lease&restype=container")
        ->assertSentWithHeaders([
            Resource::LEASE_ACTION   => 'acquire',
            Resource::LEASE_DURATION => $duration,
            Resource::LEASE_ID       => $leaseId,
        ]);
});

it('should renew a lease', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'   => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'            => ['etag'],
            'Server'          => ['server'],
            'x-ms-request-id' => ['request-id'],
            'x-ms-version'    => ['version'],
            'Date'            => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerLeaseManager($request, $container = 'container');

    expect($manager->renew($leaseId = 'leaseId'))
        ->toBeInstanceOf(ContainerLease::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2024-06-10T00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->requestId->toBe('request-id')
        ->version->toBe('version')
        ->date->format('Y-m-d\TH:i:s')->toBe('2024-06-11T00:00:00')
        ->leaseId->toBeNull();

    $request->assertPut("{$container}?comp=lease&restype=container")
        ->assertSentWithHeaders([
            Resource::LEASE_ACTION => 'renew',
            Resource::LEASE_ID     => $leaseId,
        ]);
});

it('should change a lease', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'   => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'            => ['etag'],
            'Server'          => ['server'],
            'x-ms-request-id' => ['request-id'],
            'x-ms-version'    => ['version'],
            'Date'            => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerLeaseManager($request, $container = 'container');

    expect($manager->change($fromLeaseId = 'fromLeaseId', $toLeaseId = 'leaseId'))
        ->toBeInstanceOf(ContainerLease::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2024-06-10T00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->requestId->toBe('request-id')
        ->version->toBe('version')
        ->date->format('Y-m-d\TH:i:s')->toBe('2024-06-11T00:00:00')
        ->leaseId->toBeNull();

    $request->assertPut("{$container}?comp=lease&restype=container")
        ->assertSentWithHeaders([
            Resource::LEASE_ACTION      => 'change',
            Resource::LEASE_ID          => $fromLeaseId,
            Resource::LEASE_PROPOSED_ID => $toLeaseId,
        ]);
});

it('should release a lease', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'   => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'            => ['etag'],
            'Server'          => ['server'],
            'x-ms-request-id' => ['request-id'],
            'x-ms-version'    => ['version'],
            'Date'            => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerLeaseManager($request, $container = 'container');

    expect($manager->release($leaseId = 'leaseId'))
        ->toBeInstanceOf(ContainerLease::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2024-06-10T00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->requestId->toBe('request-id')
        ->version->toBe('version')
        ->date->format('Y-m-d\TH:i:s')->toBe('2024-06-11T00:00:00')
        ->leaseId->toBeNull();

    $request->assertPut("{$container}?comp=lease&restype=container")
        ->assertSentWithHeaders([
            Resource::LEASE_ACTION => 'release',
            Resource::LEASE_ID     => $leaseId,
        ]);
});

it('should break a lease', function (?string $leaseId) {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'   => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'            => ['etag'],
            'Server'          => ['server'],
            'x-ms-request-id' => ['request-id'],
            'x-ms-version'    => ['version'],
            'Date'            => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    $manager = new ContainerLeaseManager($request, $container = 'container');

    expect($manager->break($leaseId))
        ->toBeInstanceOf(ContainerLease::class)
        ->lastModified->format('Y-m-d\TH:i:s')->toBe('2024-06-10T00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->requestId->toBe('request-id')
        ->version->toBe('version')
        ->date->format('Y-m-d\TH:i:s')->toBe('2024-06-11T00:00:00')
        ->leaseId->toBeNull();

    $request->assertPut("{$container}?comp=lease&restype=container")
        ->assertSentWithHeaders(array_filter([
            Resource::LEASE_ACTION => 'break',
            Resource::LEASE_ID     => $leaseId,
        ]));
})->with([
    'With Lease Id'    => ['leaseId'],
    'Without Lease Id' => [null],
]);
