<?php

declare(strict_types=1);

use Mockery\MockInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, BlobProperty};
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\ExpirationOption;
use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\{BlobLeaseManager, BlobManager, BlobPropertyManager, BlobTagManager};
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Exceptions\RequiredFieldException;

use function Xray\Tests\mock;

uses()->group('entities', 'blobs');

it('should throw an exception if the blob name isn\'t provided', function () {
    $blob = new Blob([]);

    expect($blob)->toBeInstanceOf(Blob::class);
})->throws(RequiredFieldException::class, 'Field [Name] is required');

it('should get the file from the blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class);

    $blob = (new Blob([
        'Name'    => $name = 'name',
        'Version' => 'version',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('get') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with($name, $options = ['foo' => 'bar'])
        ->andReturn($file = new File('filename', 'content'));

    expect($blob->get($options))
        ->toBeInstanceOf(File::class)
        ->toBe($file);
});

it('should get the blob\'s properties', function () {
    $propertyMock = mock(BlobPropertyManager::class) // @phpstan-ignore-line
        ->shouldReceive('get')
        ->atLeast()
        ->once()
        ->with($options = ['foo' => 'bar'])
        ->andReturn($blobProperty = azure_app(BlobProperty::class, ['property' => []]))
        ->getMock();

    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class) // @phpstan-ignore-line
        ->shouldReceive('properties')
        ->atLeast()
        ->once()
        ->with($name = 'name')
        ->andReturn($propertyMock)
        ->getMock();

    $blob = (new Blob([
        'Name'    => $name,
        'Version' => 'version',
    ]))->setManager($mock);

    expect($blob->getProperties($options))
        ->toBeInstanceOf(BlobProperty::class)
        ->toBe($blobProperty);
});

it('should delete the blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class);

    $blob = (new Blob([
        'Name'     => $name = 'name',
        'Version'  => 'version',
        'Snapshot' => $snapshot = '2024-01-01T00:00:00Z',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('delete') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with($name, $snapshot, $force = false)
        ->andReturnTrue();

    expect($blob->delete($force))
        ->toBeTrue();
});

it('should copy a blob to a new name', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class);

    $blob = (new Blob([
        'Name'     => $name = 'name',
        'Version'  => 'version',
        'Snapshot' => $snapshot = '2024-01-01T00:00:00Z',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('copy') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with($name, $destination = 'destination', $options = ['foo' => 'bar'], $snapshot)
        ->andReturnTrue();

    expect($blob->copy($destination, $options))
        ->toBeTrue();
});

it('should restore a deleted blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class);

    $blob = (new Blob([
        'Name'    => $name = 'name',
        'Version' => 'version',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('restore') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with($name)
        ->andReturnTrue();

    expect($blob->restore())
        ->toBeTrue();
});

it('should create a snapshot of the blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class);

    $blob = (new Blob([
        'Name'    => $name = 'name',
        'Version' => 'version',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('createSnapshot') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with($name)
        ->andReturnTrue();

    expect($blob->createSnapshot())
        ->toBeTrue();
});

it('should get tags from the blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class) // @phpstan-ignore-line
        ->shouldReceive('tags')
        ->with($name = 'name')
        ->andReturn(azure_app(BlobTagManager::class, ['containerName' => 'container', 'blobName' => $name]))
        ->getMock();

    $blob = (new Blob([
        'Name'    => $name,
        'Version' => 'version',
    ]))->setManager($mock);

    expect($blob->tags())
        ->toBeInstanceOf(BlobTagManager::class);
});

it('should lease a blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class) // @phpstan-ignore-line
        ->shouldReceive('lease')
        ->with($name = 'name')
        ->andReturn(azure_app(BlobLeaseManager::class, ['containerName' => 'container', 'blobName' => $name]))
        ->getMock();

    $blob = (new Blob([
        'Name'    => $name,
        'Version' => 'version',
    ]))->setManager($mock);

    expect($blob->lease())
        ->toBeInstanceOf(BlobLeaseManager::class);
});

it('should set the expiry of the blob', function () {
    /** @var BlobManager $mock */
    $mock = mock(BlobManager::class);

    $blob = (new Blob([
        'Name'    => $name = 'name',
        'Version' => 'version',
    ]))->setManager($mock);

    /** @var MockInterface $mock */
    $mock->shouldReceive('setExpiry') // @phpstan-ignore-line
        ->atLeast()
        ->once()
        ->with(
            $name,
            $option  = ExpirationOption::NEVER_EXPIRE,
            $expiry  = new DateTime('2024-01-01T00:00:00Z'),
            $options = ['foo' => 'bar'],
        )->andReturnTrue();

    expect($blob->setExpiry($option, $expiry, $options))
        ->toBeTrue();
});
