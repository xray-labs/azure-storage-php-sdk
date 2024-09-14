<?php

use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

pest()->group('helpers');

it('should check with function', function () {
    $called  = false;
    $content = 'test';

    expect(with($content, function (string $value) use (&$called, $content) {
        $called = true;

        expect($value)->toBe($content);
    }))->toBe($content);

    expect($called)->toBeTrue();
})->covers('with');

it('should check if it\'s running in console', function () {
    expect(is_running_in_console())->toBeTrue();
})->covers('is_running_in_console');

it('should fail when an invalid protocol is validated', function () {
    validate_protocol('invalid');
})->throws(
    InvalidArgumentException::class,
    'Invalid protocol: invalid. Valid protocols: http, https',
)->covers('validate_protocol');

it('should pass when a valid protocol is validated', function (string $protocol) {
    expect(validate_protocol($protocol))->toBeTrue();
})->with([
    'HTTP'  => ['http'],
    'HTTPS' => ['https'],
])->covers('validate_protocol');

it('should convert camel case string to be used in the headers', function (string $value, string $expected) {
    expect(str_camel_to_header($value))->toBe($expected);
})->with([
    'Pascal Case' => ['Test', 'Test'],
    'Multi Words' => ['MultiWords', 'Multi-Words'],
    'Camel Case'  => ['camelCase', 'Camel-Case'],
])->covers('str_camel_to_header');

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
])->covers('to_boolean');

it('should convert date time to RFC1123 format', function () {
    $datetime = (new DateTime('2022-05-26 04:12:36', new DateTimeZone('Asia/Jakarta')));
    $expected = (clone $datetime)->setTimezone(new DateTimeZone('GMT'));

    expect(convert_to_RFC1123($datetime))->toBe("{$expected->format('D, d M Y H:i:s')} GMT");
})->covers('convert_to_RFC1123');

it('should convert datetime to RFC3339 micro format', function () {
    $datetime = (new DateTime('2024-08-10 12:04:59', new DateTimeZone('America/New_York')));
    $expected = (clone $datetime)->setTimezone(new DateTimeZone('UTC'));

    $microseconds = $datetime->format('u');
    $microseconds = str_pad($microseconds, 7, '0', STR_PAD_LEFT);

    expect(convert_to_RFC3339_micro($datetime))->toBe("{$expected->format('Y-m-d\TH:i:s')}.{$microseconds}Z");
})->covers('convert_to_RFC3339_micro');

it('should convert to ISO format', function (string|DateTimeImmutable $datetime, $expected) {
    expect(convert_to_ISO($datetime))
        ->toBe($expected);
})->with([
    'String'            => ['2024-10-10 12:04:59', '2024-10-10T12:04:59Z'],
    'DateTimeImmutable' => [(new DateTimeImmutable('2024-10-10 12:04:59', new DateTimeZone('UTC'))), '2024-10-10T12:04:59Z'],
])->covers('convert_to_ISO');
