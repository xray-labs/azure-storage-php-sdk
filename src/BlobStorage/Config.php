<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage;

use Sjpereira\AzureStoragePhpSdk\Authentication\Contracts\Auth;
use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Sjpereira\AzureStoragePhpSdk\Converter\XmlConverter;
use Sjpereira\AzureStoragePhpSdk\Parsers\XmlParser;

final readonly class Config
{
    public string $account;

    public string $key;

    public string $version;

    public Parser $parser;

    public Converter $converter;

    public Auth $auth;

    /**
     * @param array{
     *      account: string,
     *      key: string,
     *      version: ?string,
     *      parser: ?Parser,
     *      converter: ?Converter,
     *      auth: ?Auth
     * } $config
     */
    public function __construct(array $config)
    {
        $this->account   = $config['account'];
        $this->key       = $config['key'];
        $this->version   = $config['version'] ?? Resource::VERSION;
        $this->parser    = $config['parser'] ?? new XmlParser();
        $this->converter = $config['converter'] ?? new XmlConverter();
        $this->auth      = $config['auth'] ?? new SharedKeyAuth($this);
    }
}
