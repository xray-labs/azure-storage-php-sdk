<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Support;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

define('RFC3339_MICRO', 'Y-m-d\TH:i:s.u\Z');

/**
 * Applies a closure to a value and returns the value.
 *
 * @template T
 * @param T $value The value to be passed to the closure.
 * @param \Closure(T): void $callback The closure to be applied to the value.
 * @return T The original value after the closure has been applied.
 */
function with(mixed $value, callable $callback): mixed
{
    call_user_func($callback, $value);

    return $value;
}

/**
 * Checks if the application is running in the console.
 */
function is_running_in_console(): bool
{
    return in_array(\PHP_SAPI, ['cli', 'phpdbg'], true);
}

/**
 * Validates the protocol being http or https.
 * @throws \Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException
 */
function validate_protocol(string $value): true
{
    $validProtocols = ['http', 'https'];

    if (!in_array($value, $validProtocols, true)) {
        throw InvalidArgumentException::create(sprintf(
            'Invalid protocol: %s. Valid protocols: %s',
            $value,
            implode(', ', $validProtocols),
        ));
    }

    return true;
}

/**
 * Converts a string from camelCase to snake_case.
 */
function str_camel_to_header(string $value): string
{
    return ucwords(preg_replace('/([a-z])([A-Z])/', '$1-$2', $value) ?? '', '-');
}

/**
 * Casts a value to a boolean.
 */
function to_boolean(mixed $value): bool
{
    return filter_var($value, FILTER_VALIDATE_BOOLEAN);
}

/**
 * Converts a DateTime object to RFC1123 format.
 */
function convert_to_RFC1123(DateTime $dateTime): string
{
    return (clone $dateTime)->setTimezone(new DateTimeZone('GMT'))->format('D, d M Y H:i:s') . ' GMT';
}

/**
 * Converts a DateTime object to RFC3339 micro format.
 */
function convert_to_RFC3339_micro(DateTime $dateTime): string
{
    $utcDateTime = $dateTime->setTimezone(new DateTimeZone('UTC'));

    $microseconds = $dateTime->format('u');
    $microseconds = str_pad($microseconds, 7, '0', STR_PAD_RIGHT);

    return $utcDateTime->format('Y-m-d\TH:i:s.') . $microseconds . 'Z';
}

/**
 * Converts a DateTime object to ISO format.
 */
function convert_to_ISO(DateTimeImmutable|string $dateTime): string
{
    if (is_string($dateTime)) {
        $dateTime = new DateTimeImmutable($dateTime);
    }

    $dateTime = $dateTime->setTimezone(new DateTimeZone('UTC'));

    return str_replace('+00:00', 'Z', $dateTime->format('c'));
}
