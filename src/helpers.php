<?php

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
