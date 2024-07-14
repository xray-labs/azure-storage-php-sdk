<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Sjpereira\AzureStoragePhpSdk\Converter\XmlConverter;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Sjpereira\AzureStoragePhpSdk\Parsers\XmlParser;

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
