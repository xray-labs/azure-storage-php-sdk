# Azure Storage PHP SDK

[![License](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

[![PHP CI](https://github.com/xray-labs/azure-storage-php-sdk/actions/workflows/CI.yaml/badge.svg)](https://github.com/xray-labs/azure-storage-php-sdk/actions/workflows/CI.yaml)

## Description

Integrate with Azure's cloud storage services

## Installation

```bash
composer require xray/azure-storage-php-sdk
```

## Usage

Setup Blob Storage

```php
use Xray\AzureStoragePhpSdk\BlobStorage\{BlobStorage, Config};
use Xray\AzureStoragePhpSdk\Http\Request;

$request = new Request(new Config([
    'account' => 'your_account_name',
    'key'     => 'your_account_key',
]));

$blobStorage = new BlobStorage($request);
```

[Storage Account](docs/StorageAccount.md)

## License

This project is licensed under the [MIT License](LICENSE).

## Contacts

- sjpereira2000@gmail.com
- gabrielramos791@gmail.com
- erlonsodre@gmail.com
