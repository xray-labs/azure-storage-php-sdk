<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\Parsers;

use DOMDocument;
use Sjpereira\AzureStoragePhpSdk\Parsers\Contracts\Parser;

class XmlParser implements Parser
{
    public function parse(string $source): ?DOMDocument
    {
        $xmlDoc = new DOMDocument(encoding: 'UTF-8');

        if (!$xmlDoc->loadXML($source)) {
            return null;
        }

        return $xmlDoc;
    }
}
