<?php

declare(strict_types=1);

use Sjpereira\AzureStoragePhpSdk\Authentication\SharedKeyAuth;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{Config, Resource};
use Sjpereira\AzureStoragePhpSdk\Contracts\Converter;
use Sjpereira\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;
use Sjpereira\AzureStoragePhpSdk\Parsers\XmlParser;

uses()->group('blob-storage');

it('should throw an exception if the account isn\'t provided', function () {
    expect(new Config(['key' => 'my-account-key'])) // @phpstan-ignore-line
        ->toBeInstance(Config::class);
})->throws(InvalidArgumentException::class, 'Account name must be provided.');

it('should throw an exception if the key isn\'t provided', function () {
    expect(new Config(['account' => 'account'])) // @phpstan-ignore-line
        ->toBeInstance(Config::class);
})->throws(InvalidArgumentException::class, 'Account key must be provided.');

it('should set default config value if none of the optional ones are provided', function () {
    expect(new Config(['account' => 'account', 'key' => 'key']))
        ->version->toBe(Resource::VERSION)
        ->parser->toBeInstanceOf(XmlParser::class)
        ->converter->toBeInstanceOf(Converter::class)
        ->auth->toBeInstanceOf(SharedKeyAuth::class);
});
