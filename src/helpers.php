<?php

use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

define('RFC3339_MICRO', 'Y-m-d\TH:i:s.u\Z');

if (!function_exists('with')) {
    /**
     * Applies a closure to a value and returns the value.
     *
     * @template T
     * @param T $value The value to be passed to the closure.
     * @param \Closure(T): void $callback The closure to be applied to the value.
     * @return T The original value after the closure has been applied.
     */
    function with(mixed $value, Closure $callback): mixed
    {
        $callback($value);

        return $value;
    }
}

if (!function_exists('is_running_in_console')) {
    function is_running_in_console(): bool
    {
        return in_array(\PHP_SAPI, ['cli', 'phpdbg'], true);
    }
}

if (!function_exists('validate_protocol')) {
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
}

if (!function_exists('str_camel_to_header')) {
    function str_camel_to_header(string $value): string
    {
        return ucwords(preg_replace('/([a-z])([A-Z])/', '$1-$2', $value) ?? '', '-');
    }
}

if (!function_exists('to_boolean')) {
    function to_boolean(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}

if (!function_exists('convert_to_RFC1123')) {
    function convert_to_RFC1123(DateTime $dateTime): string
    {
        return (clone $dateTime)->setTimezone(new DateTimeZone('GMT'))->format('D, d M Y H:i:s') . ' GMT';
    }
}

if (!function_exists('convert_to_RFC3339_micro')) {
    function convert_to_RFC3339_micro(DateTime $dateTime): string
    {
        $utcDateTime = $dateTime->setTimezone(new DateTimeZone('UTC'));

        $microseconds = $dateTime->format('u');
        $microseconds = str_pad($microseconds, 7, '0', STR_PAD_RIGHT);

        return $utcDateTime->format('Y-m-d\TH:i:s.') . $microseconds . 'Z';
    }
}

if (!function_exists('convert_to_ISO')) {
    function convert_to_ISO(DateTimeImmutable $dateTime): string
    {
        $dateTime = $dateTime->setTimezone(new DateTimeZone('UTC'));

        return str_replace('+00:00', 'Z', $dateTime->format('c'));
    }
}
