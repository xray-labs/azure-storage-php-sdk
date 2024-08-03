# Storage Account

Getting the storage account information

```php
use Xray\AzureStoragePhpSdk\BlobStorage\BlobStorageClient;
use Xray\AzureStoragePhpSdk\Authentication\MicrosoftEntraId;

$client = BlobStorageClient::create(new MicrosoftEntraId(
    account: 'my_account',
    directoryId: 'directory_id',
    applicationId: 'application_id',
    applicationSecret: 'application_secret',
));

$client->account()->information();
// ?^ returns Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\AccountInformation;
```
