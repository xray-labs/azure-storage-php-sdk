<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

interface Converter
{
    public function convert(array $source): string;
}
