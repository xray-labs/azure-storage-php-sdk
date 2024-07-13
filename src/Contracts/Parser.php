<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

use Sjpereira\AzureStoragePhpSdk\Exceptions\UnableToParseException;

interface Parser
{
    /**
     * @return array<object>
     * @throws UnableToParseException
     */
    public function parse(string $source): array;
}
