<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts;

use Xray\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

interface Converter
{
    /**
     * @param array<?object> $source
     * @throws UnableToConvertException
     */
    public function convert(array $source): string;
}
