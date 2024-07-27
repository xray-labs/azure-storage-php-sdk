<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel\{ContainerAccessLevel, ContainerAccessLevels};
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerAccessLevelManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake, ResponseFake};

uses()->group('blob-storage', 'managers', 'containers');

it('should list all container access levels', function () {
    $body = <<<XML
    <?xml version="1.0"?>
    <EnumerationResults>
        <SignedIdentifier>
            <Id>id</Id>
            <AccessPolicy>
                <Start>2024-06-10T00:00:00.0000000Z</Start>
                <Expiry>2025-06-10T00:00:00.0000000Z</Expiry>
                <Permission>permission</Permission>
            </AccessPolicy>
        </SignedIdentifier>
    </EnumerationResults>
    XML;

    $request = (new RequestFake())
        ->withFakeResponse(new ResponseFake($body));

    $result = (new ContainerAccessLevelManager($request))
        ->list($container = 'container', ['some' => 'value']);

    expect($result)
        ->toBeInstanceOf(ContainerAccessLevels::class)
        ->toHaveCount(1)
        ->and($result->first())
        ->toBeInstanceOf(ContainerAccessLevel::class)
        ->id->toBe('id')
        ->accessPolicyStart->format('Y-m-d')->toBe('2024-06-10')
        ->accessPolicyExpiry->format('Y-m-d')->toBe('2025-06-10')
        ->accessPolicyPermission->toBe('permission');

    $request->assertGet("{$container}?comp=acl&restype=container")
        ->assertSentWithOptions(['some' => 'value']);
});

it('should save the container access level', function () {
    $request = new RequestFake();

    $accessLevel = new ContainerAccessLevel([
        'Id'           => 'id',
        'AccessPolicy' => [
            'Start'      => '2024-06-10T00:00:00.0000000Z',
            'Expiry'     => '2025-06-10T00:00:00.0000000Z',
            'Permission' => 'permission',
        ],
    ]);

    expect((new ContainerAccessLevelManager($request))->save($container = 'container', $accessLevel, ['some' => 'value']))
        ->toBeTrue();

    $request->assertPut("{$container}?comp=acl&restype=container", $accessLevel->toXML())
        ->assertSentWithOptions(['some' => 'value'])
        ->assertSentWithHeaders([Resource::CONTENT_TYPE => 'application/xml']);
});
