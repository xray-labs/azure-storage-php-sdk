<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\Authentication\Contracts\Auth;
use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Sjpereira\AzureStoragePhpSdk\Converter\XmlConverter;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Sjpereira\AzureStoragePhpSdk\Parsers\XmlParser;

/**
 * @phpstan-type ConfigType array{account: string, key: string, version?: string, parser?: Parser, converter?: Converter, auth?: Auth}
 */
final readonly class Config
{
    public string $account;

    public string $key;

    public string $version;

    public Parser $parser;

    public Converter $converter;

    public Auth $auth;

    /**
     * @param ConfigType $config
     * @throws InvalidArgumentException
     */
    public function __construct(array $config)
    {
        if (empty($config['account'] ?? null)) {
            throw InvalidArgumentException::create('Account name must be provided.');
        }

        if (empty($config['key'] ?? null)) {
            throw InvalidArgumentException::create('Account key must be provided.');
        }

        $this->account   = $config['account'];
        $this->key       = $config['key'];
        $this->version   = $config['version'] ?? Resource::VERSION;
        $this->parser    = $config['parser'] ?? new XmlParser();
        $this->converter = $config['converter'] ?? new XmlConverter();
        $this->auth      = $config['auth'] ?? new SharedKeyAuth($this);
    }
}
