<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Converter;

use SimpleXMLElement;
use Xray\AzureStoragePhpSdk\Contracts\Converter;
use Xray\AzureStoragePhpSdk\Exceptions\UnableToConvertException;

class XmlConverter implements Converter
{
    /**
     * @param array<string, mixed> $source
     * @throws UnableToConvertException
    */
    public function convert(array $source): string
    {
        /** @var string|false $rootTag */
        $rootTag = current(array_keys($source));

        if (!$rootTag || count($source) > 1) {
            throw UnableToConvertException::create('Unable to convert. The root tag is missing.');
        }

        $xml = new SimpleXMLElement("<{$rootTag}/>");

        if (is_array($source[$rootTag])) {
            $this->generateXmlRecursively($source[$rootTag], $xml);
        }

        $result = $xml->asXML();

        if ($result === false) {
            throw UnableToConvertException::create('Failed to convert XML'); // @codeCoverageIgnore
        }

        return $result;
    }

    /** @param array<string|int, int|string|float|bool|null|array<string|int, int|string|float|bool|null>> $source */
    protected function generateXmlRecursively(array $source, SimpleXMLElement &$xml): void
    {
        foreach ($source as $key => $value) {
            if (is_array($value)) {
                if (is_int($key)) {
                    $this->generateXmlRecursively($value, $xml);
                } else {
                    $child = $xml->addChild($key);
                    $this->generateXmlRecursively($value, $child);
                }

                continue;
            }

            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            }

            $xml->addChild((string)$key, htmlspecialchars((string) $value));
        }
    }
}
