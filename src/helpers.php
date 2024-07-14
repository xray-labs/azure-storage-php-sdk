<?php

define('RFC3339_MICRO', 'Y-m-d\TH:i:s.u\Z');

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
    function convert_to_RFC3339_micro(DateTimeImmutable $dateTime): string
    {
        $utcDateTime = $dateTime->setTimezone(new DateTimeZone('UTC'));

        $microseconds = $dateTime->format('u');
        $microseconds = str_pad($microseconds, 7, '0', STR_PAD_RIGHT);

        return $utcDateTime->format('Y-m-d\TH:i:s.') . $microseconds . 'Z';
    }
}
