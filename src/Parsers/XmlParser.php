<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\Parsers;

use DOMDocument;
use Sjpereira\AzureStoragePhpSdk\Parsers\Contracts\Parser;

class XmlParser implements Parser
{
    /**
     * Undocumented function
     *
     * @param string $source
     * @return array
     */
    public function parse(string $source): array
    {
        $source = simplexml_load_string($source);

        $array = (array) json_decode(json_encode($source) ?: '', true);

        array_walk_recursive($array, function(&$item) {
            $item = $item === [] ? null : $item;
        });
        
        return $array;
    }
}
