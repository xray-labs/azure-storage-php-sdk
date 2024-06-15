<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Support\Collection;

uses()->group('supports');

it('should have the correct architecture', function () {
    expect(Collection::class)
        ->toImplement([IteratorAggregate::class, ArrayAccess::class, JsonSerializable::class]);
});

it('should be accessible', function (string $method, mixed $expected, ?int $param = null) {
    $items = [
        (object)['id' => 1],
        (object)['id' => 2],
        (object)['id' => 3],
    ];

    $result = (new Collection($items))
        ->{$method}($param);

    if ($expected instanceof Closure) {
        $result   = $expected($result);
        $expected = true;
    }

    expect($result)
        ->toBe($expected);
})->with([
    'Get All Items'  => ['all', fn (array $values): bool => count($values) === 3],
    'Get First Item' => ['first', fn (object $value): bool => $value->id === 1],
    'Get Last Item'  => ['last', fn (object $value): bool => $value->id === 3],
    'Get Item'       => ['get', fn (object $value): bool => $value->id === 2, 1],
    'Count Items'    => ['count', 3],
    'Is Empty'       => ['isEmpty', false],
    'Is Not Empty'   => ['isNotEmpty', true],
]);

it('should be treated as an array', function (Closure $callback) {
    $items = [
        (object)['id' => 1],
        (object)['id' => 2],
        (object)['id' => 3],
    ];

    $collection = new Collection($items);

    expect($callback($collection))
        ->toBeTrue();
})->with([
    'Key Exists' => [fn (Collection $collection): bool => isset($collection[2])],
    'Get Key'    => [fn (Collection $collection): bool => $collection[1]->id === 2],
    'Set Key'    => [function (Collection $collection): bool {
        $collection[4] = (object)['id' => 4];

        return $collection[4]->id === 4;
    }],
    'Unset Key' => [function (Collection $collection): bool {
        unset($collection[2]);

        return !isset($collection[2]);
    }],
]);

it('should be able to serialize as JSON', function () {
    $items = [
        (object)['id' => 1],
        (object)['id' => 2],
        (object)['id' => 3],
    ];

    $result = json_encode(new Collection($items));

    expect($result)
        ->toBe('[{"id":1},{"id":2},{"id":3}]');
});

it('should be able to iterate over', function () {
    $items = [
        (object)['id' => 1],
        (object)['id' => 2],
        (object)['id' => 3],
    ];

    $iterations = 0;

    foreach (new Collection($items) as $index => $item) {
        expect($item->id)->toBe($index + 1);

        $iterations++;
    }

    expect($iterations)->toBe(3);
});
