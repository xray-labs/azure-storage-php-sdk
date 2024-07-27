<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage;

use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Xray\AzureStoragePhpSdk\Converter\XmlConverter;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Xray\AzureStoragePhpSdk\Parsers\XmlParser;

/**
 * @phpstan-type ConfigType array{version?: string, parser?: Parser, converter?: Converter}
 */
final readonly class Config
{
    public string $version;

    public Parser $parser;

    public Converter $converter;

    /**
     * @param ConfigType $config
     * @throws InvalidArgumentException
     */
    public function __construct(public Auth $auth, array $config = [])
    {
        $this->version   = $config['version'] ?? Resource::VERSION;
        $this->parser    = $config['parser'] ?? new XmlParser();
        $this->converter = $config['converter'] ?? new XmlConverter();
    }
}
