<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

use Sjpereira\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

interface Converter
{
    /**
     * @param array<?object> $source
     * @throws UnableToConvertException
     */
    public function convert(array $source): string;
}
