<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts;

use Xray\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

interface Xmlable
{
    /**
     * @return string
     * @throws UnableToConvertException
     */
    public function toXml(): string;
}
