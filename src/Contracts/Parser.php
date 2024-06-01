<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

interface Parser
{
    /** @return array<object> */
    public function parse(string $source): array;
}
