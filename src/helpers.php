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
