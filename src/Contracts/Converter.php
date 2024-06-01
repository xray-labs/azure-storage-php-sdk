<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

interface Converter
{
    /** @param array<?object> $source */
    public function convert(array $source): string;
}
