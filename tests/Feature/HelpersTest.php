<?php

uses()->group('helpers');

it('should check with function', function () {
    $called  = false;
    $content = 'test';

    expect(with($content, function (string $value) use (&$called, $content) {
        $called = true;

        expect($value)->toBe($content);
    }))->toBe($content);

    expect($called)->toBeTrue();
});

it('should check if it\'s running in console', function () {
    expect(is_running_in_console())->toBeTrue();
});

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

it('should convert date time to RFC1123 format', function () {
    $datetime = (new DateTime('2022-05-26 04:12:36', new DateTimeZone('Asia/Jakarta')));
    $expected = (clone $datetime)->setTimezone(new DateTimeZone('GMT'));

    expect(convert_to_RFC1123($datetime))->toBe("{$expected->format('D, d M Y H:i:s')} GMT");
});

it('should convert datetime to RFC3339 micro format', function () {
    $datetime = (new DateTime('2024-08-10 12:04:59', new DateTimeZone('America/New_York')));
    $expected = (clone $datetime)->setTimezone(new DateTimeZone('UTC'));

    $microseconds = $datetime->format('u');
    $microseconds = str_pad($microseconds, 7, '0', STR_PAD_LEFT);

    expect(convert_to_RFC3339_micro($datetime))->toBe("{$expected->format('Y-m-d\TH:i:s')}.{$microseconds}Z");
});
