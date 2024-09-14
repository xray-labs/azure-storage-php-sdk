<?php

declare(strict_types=1);

use Pest\Expectation;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\Properties;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\{Container, ContainerProperties, Containers};
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container\{ContainerAccessLevelManager, ContainerLeaseManager, ContainerMetadataManager};
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Xray\AzureStoragePhpSdk\Fakes\Http\{RequestFake, ResponseFake};
use Xray\AzureStoragePhpSdk\Http\Response as BaseResponse;

pest()->group('blob-storage', 'managers', 'containers');
covers(ContainerManager::class);

it('should get container\'s managers', function (string $method, string $class) {
    $request = new RequestFake();

    expect((new ContainerManager($request))->{$method}())
        ->toBeInstanceOf($class); // @phpstan-ignore-line
})->with([
    'Access Level' => ['accessLevel', ContainerAccessLevelManager::class],
    'Metadata'     => ['metadata', ContainerMetadataManager::class],
]);

it('should get container properties', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(headers: [
            'Last-Modified'                                  => ['2024-06-10T00:00:00.0000000Z'],
            'ETag'                                           => ['etag'],
            'Server'                                         => ['server'],
            'x-ms-request-id'                                => ['request-id'],
            'x-ms-version'                                   => ['version'],
            'x-ms-lease-status'                              => ['lease-status'],
            'x-ms-lease-state'                               => ['lease-state'],
            'x-ms-has-immutability-policy'                   => ['true'],
            'x-ms-has-legal-hold'                            => ['true'],
            'x-ms-immutable-storage-with-versioning-enabled' => ['true'],
            'x-ms-default-encryption-scope'                  => ['default-encryption-scope'],
            'x-ms-deny-encryption-scope-override'            => ['true'],
            'Date'                                           => ['2024-06-11T00:00:00.0000000Z'],
        ]));

    expect((new ContainerManager($request))->getProperties($container = 'container', ['some' => 'value']))
        ->toBeInstanceOf(ContainerProperties::class)
        ->lastModified->format('Y-m-d H:i:s')->toBe('2024-06-10 00:00:00')
        ->eTag->toBe('etag')
        ->server->toBe('server')
        ->xMsRequestId->toBe('request-id')
        ->xMsVersion->toBe('version')
        ->xMsLeaseStatus->toBe('lease-status')
        ->xMsLeaseState->toBe('lease-state')
        ->xMsHasImmutabilityPolicy->toBeTrue()
        ->xMsHasLegalHold->toBeTrue()
        ->xMsImmutableStorageWithVersioningEnabled->toBeTrue()
        ->xMsDefaultEncryptionScopeOverride->toBe('default-encryption-scope')
        ->xMsDenyEncryptionScopeOverride->toBeTrue()
        ->date->format('Y-m-d H:i:s')->toBe('2024-06-11 00:00:00');

    $request->assertGet("{$container}?restype=container")
        ->assertSentWithOptions(['some' => 'value']);
});

it('should list all the containers', function (bool $withDeleted) {
    $xml = <<<XML
    <?xml version="1.0" encoding="utf-8"?>
    <EnumerationResults>
        <Containers>
            <Container>
                <Name>name1</Name>
                <Deleted>false</Deleted>
                <Version>version</Version>
                <Properties>
                    <Last-Modified>2024-06-10T00:00:00.0000000Z</Last-Modified>
                    <Etag>etag</Etag>
                    <LeaseStatus>lease-status</LeaseStatus>
                    <LeaseState>lease-state</LeaseState>
                    <DefaultEncryptionScope>default-encryption-scope</DefaultEncryptionScope>
                    <DenyEncryptionScopeOverride>true</DenyEncryptionScopeOverride>
                    <HasImmutabilityPolicy>true</HasImmutabilityPolicy>
                    <HasLegalHold>true</HasLegalHold>
                    <ImmutableStorageWithVersioningEnabled>true</ImmutableStorageWithVersioningEnabled>
                    <DeletedTime>2024-06-11T00:00:00.0000000Z</DeletedTime>
                    <RemainingRetentionDays>10</RemainingRetentionDays>
                </Properties>
            </Container>
            <Container>
                <Name>name2</Name>
                <Deleted>false</Deleted>
                <Version>version</Version>
                <Properties>
                    <Last-Modified>2024-06-10T00:00:00.0000000Z</Last-Modified>
                    <Etag>etag</Etag>
                    <LeaseStatus>lease-status</LeaseStatus>
                    <LeaseState>lease-state</LeaseState>
                    <DefaultEncryptionScope>default-encryption-scope</DefaultEncryptionScope>
                    <DenyEncryptionScopeOverride>true</DenyEncryptionScopeOverride>
                    <HasImmutabilityPolicy>true</HasImmutabilityPolicy>
                    <HasLegalHold>true</HasLegalHold>
                    <ImmutableStorageWithVersioningEnabled>true</ImmutableStorageWithVersioningEnabled>
                    <DeletedTime>2024-06-11T00:00:00.0000000Z</DeletedTime>
                    <RemainingRetentionDays>10</RemainingRetentionDays>
                </Properties>
            </Container>
        </Containers>
    </EnumerationResults>
    XML;

    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake($xml));

    $result = (new ContainerManager($request))->list(['some' => 'value'], $withDeleted);

    expect($result)
        ->toBeInstanceOf(Containers::class)
        ->toHaveCount(2)
        ->each(function (Expectation $container, int $index): void {
            /** @var Container $value */
            $value = $container->value;

            $container->toBeInstanceOf(Container::class)
                ->name->toBe('name' . ($index + 1))
                ->deleted->toBeFalse()
                ->version->toBe('version')
                ->and($value->properties)
                ->toBeInstanceOf(Properties::class)
                ->lastModified->format('Y-m-d H:i:s')->toBe('2024-06-10 00:00:00')
                ->eTag->toBe('etag')
                ->leaseStatus->toBe('lease-status')
                ->leaseState->toBe('lease-state')
                ->defaultEncryptionScope->toBe('default-encryption-scope')
                ->denyEncryptionScopeOverride->toBeTrue()
                ->hasImmutabilityPolicy->toBeTrue()
                ->hasLegalHold->toBeTrue()
                ->immutableStorageWithVersioningEnabled->toBeTrue()
                ->deletedTime->format('Y-m-d H:i:s')->toBe('2024-06-11 00:00:00')
                ->remainingRetentionDays->toBe(10);
        });

    $request->assertGet('?comp=list' . ($withDeleted ? '&include=deleted' : ''))
        ->assertSentWithOptions(['some' => 'value']);
})->with([
    'Without Deleted' => [false],
    'With Deleted'    => [true],
]);

it('should not be able to request when a container name is invalid', function (string $method) {
    $request = (new RequestFake());

    $container = 'container#Name.';

    expect(fn () => (new ContainerManager($request))->{$method}($container, 'version'))
        ->toThrow(InvalidArgumentException::class, "Invalid container name: {$container}");
})->with([
    'When Lease'   => ['lease'],
    'When Create'  => ['create'],
    'When Delete'  => ['delete'],
    'When Restore' => ['restore'],
]);

it('should lease a container', function () {
    $request = (new RequestFake());

    expect((new ContainerManager($request))->lease('container'))
        ->toBeInstanceOf(ContainerLeaseManager::class);
});

it('should create a new container', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    expect((new ContainerManager($request))->create($container = 'container'))
        ->toBeTrue();

    $request->assertPut("{$container}?restype=container");
});

it('should delete an existing container', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_ACCEPTED));

    expect((new ContainerManager($request))->delete($container = 'container'))
        ->toBeTrue();

    $request->assertDelete("{$container}?restype=container");
});

it('should restore a deleted container', function () {
    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake(statusCode: BaseResponse::STATUS_CREATED));

    expect((new ContainerManager($request))->restore($container = 'container', $version = 'version'))
        ->toBeTrue();

    $request->assertPut("{$container}?comp=undelete&restype=container")
        ->assertSentWithHeaders([
            Resource::DELETE_CONTAINER_NAME    => $container,
            Resource::DELETE_CONTAINER_VERSION => $version,
        ]);
});
