<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Parsers;

use Sjpereira\AzureStoragePhpSdk\Contracts\Parser;

class XmlParser implements Parser
{
    public function parse(string $source): array
    {
        $source = simplexml_load_string($source);

        $parsed = (array) json_decode(json_encode($source) ?: '', true);

        array_walk_recursive(
            $parsed,
            fn (mixed &$item) => $item = $item === [] ? null : $item,
        );

        return $parsed;
    }
}
