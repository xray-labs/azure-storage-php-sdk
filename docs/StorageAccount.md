# Storage Account

Getting the storage account information

```php
use Xray\AzureStoragePhpSdk\BlobStorage\{BlobStorage, Config};
use Xray\AzureStoragePhpSdk\Http\Request;

$request = new Request(new Config([
    'account' => 'your_account_name',
    'key'     => 'your_account_key',
]));

$blobStorage = new BlobStorage($request);

$blobStorage->account()->information();
// returns Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\AccountInformation;
```
