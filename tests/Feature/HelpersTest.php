<?php

it('should convert camel case string to be used in the headers', function (string $value, string $expected) {
    expect(str_camel_to_header($value))->toBe($expected);
})->with([
    'Pascal Case' => ['Test', 'Test'],
    'Multi Words' => ['MultiWords', 'Multi-Words'],
    'Camel Case'  => ['camelCase', 'Camel-Case'],
]);

it('should convert value to boolean type', function (mixed $value, bool $expected) {
    expect(to_boolean($value))->toBe($expected);
})->with([
    'Empty Array'  => [[], false],
    'Empty String' => ['', false],
    'Empty Object' => [(object)[], false],
    'True'         => [true, true],
    'False'        => [false, false],
    'Null'         => [null, false],
    'Number 1'     => [1, true],
    'Number 0'     => [0, false],
    'String'       => ['string', false],
    'Object'       => [(object)['test' => 'test'], false],
    'Array'        => [[1, 2, 3], false],
]);
