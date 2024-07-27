<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Xray\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Xray\AzureStoragePhpSdk\Contracts\Converter;
use Xray\AzureStoragePhpSdk\Parsers\XmlParser;

uses()->group('blob-storage');

it('should set default config value if none of the optional ones are provided', function () {
    expect(new Config(new SharedKeyAuth('account', 'key')))
        ->version->toBe(Resource::VERSION)
        ->parser->toBeInstanceOf(XmlParser::class)
        ->converter->toBeInstanceOf(Converter::class)
        ->auth->toBeInstanceOf(SharedKeyAuth::class);
});
