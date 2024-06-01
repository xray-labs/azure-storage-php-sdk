<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Converter;

use SimpleXMLElement;
use Sjpereira\AzureStoragePhpSdk\Contracts\Converter;
use Sjpereira\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

class XmlConverter implements Converter
{
    /**
     * @param array<string, mixed> $source
     * @throws UnableToConvertException
    */
    public function convert(array $source): string
    {
        $rootTag = array_keys($source)[0];
        $xml     = new SimpleXMLElement($rootTag ? '<' . $rootTag . '/>' : '<root/>');

        if (is_array($source[$rootTag])) {
            $this->generateXmlRecursively($source[$rootTag], $xml);
        }

        $result = $xml->asXML();

        if ($result === false) {
            throw UnableToConvertException::create('Failed to convert XML');
        }

        return $result;
    }

    /** @param array<string, mixed> $source */
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

            $xml->addChild((string)$key, htmlspecialchars(is_string($value) ? $value : ''));
        }
    }
}
