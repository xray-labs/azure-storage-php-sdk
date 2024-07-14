<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Contracts\Converter;
use Sjpereira\AzureStoragePhpSdk\Parsers\XmlParser;

uses()->group('blob-storage');

it('should set default config value if none of the optional ones are provided', function () {
    expect(new Config(new SharedKeyAuth('account', 'key')))
        ->version->toBe(Resource::VERSION)
        ->parser->toBeInstanceOf(XmlParser::class)
        ->converter->toBeInstanceOf(Converter::class)
        ->auth->toBeInstanceOf(SharedKeyAuth::class);
});
