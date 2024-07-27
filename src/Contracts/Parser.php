<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts;

use Xray\AzureStoragePhpSdk\Exceptions\UnableToParseException;

interface Parser
{
    /**
     * @return array<object>
     * @throws UnableToParseException
     */
    public function parse(string $source): array;
}
