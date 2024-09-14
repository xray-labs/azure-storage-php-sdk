<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Contracts\Arrayable;
use Xray\AzureStoragePhpSdk\Support\Collection;

pest()->group('supports');
covers(Collection::class);

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
    'Get First Item' => ['first', fn (object $value): bool => $value->id === 1], // @phpstan-ignore-line
    'Get Last Item'  => ['last', fn (object $value): bool => $value->id === 3], // @phpstan-ignore-line
    'Get Item'       => ['get', fn (object $value): bool => $value->id === 2, 1], // @phpstan-ignore-line
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
    'Get Key'    => [fn (Collection $collection): bool => $collection[1]->id === 2], // @phpstan-ignore-line
    'Set Key'    => [function (Collection $collection): bool {
        $collection[4] = (object)['id' => 4];

        return $collection[4]->id === 4; // @phpstan-ignore-line
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

it('should get the first item', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ];

    $findItem = function (int $id): callable {
        return function (object $item) use ($id): bool {
            /** @var object{id: int, text: string} $item */
            return $item->id === $id;
        };
    };

    expect(new Collection($items))
        ->first()->text->toBe('test')
        ->first($findItem(2))->text->toBe('something')
        ->first($findItem(4))->toBeNull()
        ->first($findItem(4), fn () => (object) ['text' => 'new'])->text->toBe('new')
        ->and(new Collection())
        ->first()->toBeNull();
});

it('should get the last item', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ];

    $findItem = function (int $id): callable {
        return function (object $item) use ($id): bool {
            /** @var object{id: int, text: string} $item */
            return $item->id === $id;
        };
    };

    expect(new Collection($items))
        ->last()->text->toBe('other')
        ->last($findItem(2))->text->toBe('something')
        ->last($findItem(4))->toBeNull()
        ->last($findItem(4), fn () => (object) ['text' => 'new'])->text->toBe('new')
        ->and(new Collection())
        ->last()->toBeNull();
});

it('should be able to get an item out of the collection', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ];

    expect(new Collection($items))
        ->get(1)->text->toBe('something')
        ->get(3)->toBeNull()
        ->get(4, fn () => 'test')->toBe('test');
});

it('should get the collection\'s keys', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ];

    expect(new Collection($items))
        ->keys()->all()->toBe([0, 1, 2]);
});

it('should push items into the collection', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
    ];

    $collection = (new Collection($items))
        ->push((object)['id' => 2, 'text' => 'something']);

    expect($collection)
        ->first()->id->toBe(1)
        ->last()->id->toBe(2);
});

it('should merge items into the collection', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
    ];

    $collection = (new Collection($items))
        ->merge([(object)['id' => 3, 'text' => 'something']]);

    expect($collection)
        ->first()->id->toBe(1)
        ->last()->id->toBe(3);
});

it('should concat items to the collection', function () {
    $items = [
        (object)['id' => 1, 'text' => 'test'],
    ];

    $collection = (new Collection($items))
        ->concat([(object)['id' => 4, 'text' => 'something']]);

    expect($collection)
        ->first()->id->toBe(1)
        ->last()->id->toBe(4);
});

it('should put a new value into a collection key', function () {
    $collection = new Collection([
        (object)['id' => 1, 'text' => 'test'],
    ]);

    $collection->put(0, (object)['id' => 2, 'text' => 'something']);

    expect($collection)
        ->first()->id->toBe(2);
});

it('should forget a value from the collection', function () {
    $collection = new Collection([
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ]);

    $collection->forget(1);

    expect($collection)
        ->count()->toBe(2);

    $collection->forget([0, 2]);

    expect($collection)
        ->isEmpty()->toBeTrue();
});

it('should pull an item out of the collection', function () {
    $collection = new Collection([
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ]);

    expect($collection->pull(1))
        ->id->toBe(2);

    expect($collection)
        ->count()->toBe(2);
});

it('should map the collection', function () {
    $collection = new Collection([
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ]);

    $result = $collection->map(fn (object $item) => $item->text);

    expect($result)
        ->all()
        ->toBe(['test', 'something', 'other']);
});

it('should loop over the collection', function () {
    $collection = new Collection([
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ]);

    $iterations = 0;

    $collection->each(function (object $item, int $index) use (&$iterations) {
        $iterations++;

        expect($item)
            ->id->toBe($index + 1);
    });

    expect($iterations)->toBe(3);
});

it('should filter the collection', function () {
    $collection = new Collection([
        (object)['id' => 1, 'text' => 'test'],
        (object)['id' => 2, 'text' => 'something'],
        (object)['id' => 3, 'text' => 'other'],
    ]);

    expect($collection->filter(fn (object $item) => $item->id > 1))
        ->count()->toBe(2)
        ->first()->id->toBe(2)
        ->last()->id->toBe(3);

    $collection = new Collection(['test', '', null, 0]);

    expect($collection->filter())
        ->count()->toBe(1)
        ->first()->toBe('test');
});

it('should get collection as array', function () {
    $collection = new Collection([
        'test',
        new class () implements Arrayable {
            /** @return array<string> */
            public function toArray(): array
            {
                return ['array'];
            }
        },
        new class () implements JsonSerializable {
            /** @return array<string> */
            public function jsonSerialize(): array
            {
                return ['json'];
            }
        },
    ]);

    expect($collection->toArray())
        ->toBe(['test', ['array'], ['json']]);
});
