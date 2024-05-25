<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\Parsers\Contracts;

interface Parser
{
    public function parse(string $source);
}
