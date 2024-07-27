<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Parsers;

use Xray\AzureStoragePhpSdk\Contracts\Parser;
use Xray\AzureStoragePhpSdk\Exceptions\UnableToParseException;

class XmlParser implements Parser
{
    /**
     * @return array<string, mixed>
     * @throws UnableToParseException
     */
    public function parse(string $source): array
    {
        $xml = simplexml_load_string($source);

        if ($xml === false) {
            throw UnableToParseException::create($source);
        }

        $parsed = (array) json_decode(json_encode($xml) ?: '', true);

        array_walk_recursive(
            $parsed,
            fn (mixed &$item) => $item = $item === [] ? null : $item,
        );

        return $parsed;
    }
}
