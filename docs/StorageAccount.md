# Storage Account

Getting the storage account information

```php
use Sjpereira\AzureStoragePhpSdk\BlobStorage\{BlobStorage, Config};
use Sjpereira\AzureStoragePhpSdk\Http\Request;

$request = new Request(new Config([
    'account' => 'your_account_name',
    'key'     => 'your_account_key',
]));

$blobStorage = new BlobStorage($request);

$blobStorage->account()->information();
// returns Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\AccountInformation;
```
