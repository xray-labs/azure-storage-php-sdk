<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Xray\AzureStoragePhpSdk\Contracts\Converter;
use Xray\AzureStoragePhpSdk\Parsers\XmlParser;

uses()->group('blob-storage');

it('should set default config value if none of the optional ones are provided', function () {
    expect(new Config())
        ->version->toBe(Resource::VERSION)
        ->parser->toBeInstanceOf(XmlParser::class)
        ->converter->toBeInstanceOf(Converter::class);
});
