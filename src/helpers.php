<?php

if (!function_exists('str_camel_to_header')) {
    function str_camel_to_header(string $string): string
    {
        return ucwords(preg_replace('/([a-z])([A-Z])/', '$1-$2', $string), '-');
    }
}
