<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Contracts;

interface Parser
{
    /**
     * Undocumented function
     *
     * @param string $source
     * @return array<T>
     */
    public function parse(string $source): array;
}
