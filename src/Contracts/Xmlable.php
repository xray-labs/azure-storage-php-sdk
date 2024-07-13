<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

use Sjpereira\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

interface Xmlable
{
    /**
     * @return string
     * @throws UnableToConvertException
     */
    public function toXml(): string;
}
