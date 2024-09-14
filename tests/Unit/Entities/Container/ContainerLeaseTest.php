<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\ContainerLease;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerLeaseManager;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

use function Xray\Tests\mock;

pest()->group('entities', 'containers');
covers(ContainerLease::class);

it('should renew the container lease', function () {
    /** @var ContainerLeaseManager $mock */
    $mock = mock(ContainerLeaseManager::class);

    $containerLease = (new ContainerLease([
        'Last-Modified' => '2024-06-10T00:00:00.0000000Z',
        'ETag'          => 'etag',
        'Server'        => 'server',
        'Version'       => 'version',
        'Date'          => '2024-06-10T00:00:00.0000000Z',
        'x-ms-lease-id' => $leaseId = 'leaseId',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('renew') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with($leaseId)
        ->andReturn($containerLease);

    expect($containerLease->renew())
        ->toBeInstanceOf(ContainerLease::class);
});

it('should change/release/break the container lease', function (string $method, ?string $toLeaseId = null) {
    /** @var ContainerLeaseManager $mock */
    $mock = mock(ContainerLeaseManager::class);

    $containerLease = (new ContainerLease([
        'Last-Modified' => '2024-06-10T00:00:00.0000000Z',
        'ETag'          => 'etag',
        'Server'        => 'server',
        'Version'       => 'version',
        'Date'          => '2024-06-10T00:00:00.0000000Z',
        'x-ms-lease-id' => $fromLeaseId = 'leaseId',
    ]))->setManager($mock);

    $params = array_filter([$fromLeaseId, $toLeaseId]);

    /** @var MockInterface $mock */
    $mock->shouldReceive($method) // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with(...$params)
        ->andReturn($containerLease);

    expect($containerLease->{$method}(count($params) === 2 ? $toLeaseId : $fromLeaseId))
        ->toBeInstanceOf(ContainerLease::class);
})->with([
    'Change'  => ['change', 'toLeaseId'],
    'Release' => ['release'],
    'Break'   => ['break'],
]);

it('should ensure the lease id is set', function () {
    $containerLease = new ContainerLease([
        'Last-Modified' => '2024-06-10T00:00:00.0000000Z',
        'ETag'          => 'etag',
        'Server'        => 'server',
        'Version'       => 'version',
        'Date'          => '2024-06-10T00:00:00.0000000Z',
    ]);

    expect($containerLease->renew())->toBeInstanceOf(ContainerLease::class);
})->throws(RequiredFieldException::class, 'Field [leaseId] is required');
