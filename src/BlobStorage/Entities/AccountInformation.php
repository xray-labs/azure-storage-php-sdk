<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities;

use DateTime;

readonly class AccountInformation
{
    public string $server;

    public string $xMsRequestId;

    public string $xMsVersion;

    public string $xMsSkuName;

    public string $xMsAccountKind;

    public string $xMsIsHnsEnabled;

    public DateTime $date;

    public function __construct(array $accountInformation)
    {
        $this->server          = $accountInformation['Server'] ?? '';
        $this->xMsRequestId    = $accountInformation['x-ms-request-id'] ?? '';
        $this->xMsVersion      = $accountInformation['x-ms-version'] ?? '';
        $this->xMsSkuName      = $accountInformation['x-ms-sku-name'] ?? '';
        $this->xMsAccountKind  = $accountInformation['x-ms-account-kind'] ?? '';
        $this->xMsIsHnsEnabled = $accountInformation['x-ms-is-hns-enabled'] ?? '';
        $this->date            = new DateTime($accountInformation['Date'] ?? 'now');
    }
}
