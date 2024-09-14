<?php

use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\BlobStorage\Queries\BlobTagQuery as QueriesBlobTagQuery;
use Xray\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequiredFieldException};
use Xray\AzureStoragePhpSdk\Tests\Http\{RequestFake};

pest()->group('blob-storage', 'queries');
covers(QueriesBlobTagQuery::class);

it('should create a query', function () {
    $request = (new RequestFake());

    $manager = (new BlobManager($request, 'container'));

    $query = (new QueriesBlobTagQuery($manager))
        ->where('tag', 'value')
        ->where('sequence', '>', '2')
        ->where('sequence', '<', '10');

    expect((fn () => $this->wheres)->call($query))
        ->toHaveCount(3)
        ->toEqual([
            ['tag' => 'tag', 'operator' => '=', 'value' => 'value'],
            ['tag' => 'sequence', 'operator' => '>', 'value' => '2'],
            ['tag' => 'sequence', 'operator' => '<', 'value' => '10'],
        ]);
});

it('should set the whenBuild callback', function () {
    $request = (new RequestFake());

    $manager = (new BlobManager($request, 'container'));

    $query = (new QueriesBlobTagQuery($manager))
        ->whenBuild(function (string $query): object {
            return (object) ['query' => $query];
        });

    expect((fn () => $this->callback)->call($query))
        ->toBeInstanceOf(Closure::class);
});

it('should throw an exception when the whenBuild callback is not set', function () {
    $request = (new RequestFake());

    $manager = (new BlobManager($request, 'container'));

    $query = (new QueriesBlobTagQuery($manager));

    $query->build();
})->throws(RequiredFieldException::class, 'Field [callback] is required');

it('should build the query', function () {
    $request = (new RequestFake());

    $manager = (new BlobManager($request, 'container'));

    $query = (new QueriesBlobTagQuery($manager))
        ->where('tag', 'value')
        ->where('sequence', '>', '2')
        ->where('sequence', '<', '10')
        ->whenBuild(function (string $query): object {
            return (object) ['query' => $query];
        });

    expect($query->build())
        ->toEqual((object) ['query' => '%22sequence%22%3E%272%27AND%22sequence%22%3C%2710%27AND%22tag%22%3D%27value%27']);
});

it('should throw an exception when the operator is invalid', function () {
    $request = (new RequestFake());

    $manager = (new BlobManager($request, 'container'));

    $query = (new QueriesBlobTagQuery($manager));

    $query->where('tag', 'invalid', 'value');
})->throws(InvalidArgumentException::class, 'Invalid operator: invalid');
