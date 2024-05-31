<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

interface Converter
{
    /**
     * Undocumented function
     *
     * @param array<?object> $source
     * @return string
     */
    public function convert(array $source): string;
}
