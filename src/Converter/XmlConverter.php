<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Converter;

use SimpleXMLElement;
use Sjpereira\AzureStoragePhpSdk\Contracts\Converter;

class XmlConverter implements Converter
{
    public function convert(array $source): string
    {
        $rootTag = array_keys($source)[0];
        $xml     = new SimpleXMLElement($rootTag ? '<' . $rootTag . '/>' : '<root/>');

        $this->generateXmlRecursively($source[$rootTag], $xml);

        $result = $xml->asXML();

        if ($result === false) {
            throw new \RuntimeException('Failed to convert XML'); // TODO: Better exception
        }

        return $result;
    }

    protected function generateXmlRecursively(array $source, SimpleXMLElement &$xml): void
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                $child = $xml->addChild((string)$key);
                $this->generateXmlRecursively($value, $child);

                continue;
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $xml->addChild((string)$key, htmlspecialchars((string) $value));
        }
    }
}
