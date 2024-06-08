<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account;

use DateTimeImmutable;

final readonly class AccountInformation
{
    public string $server;

    public string $xMsRequestId;

    public string $xMsVersion;

    public string $xMsSkuName;

    public string $xMsAccountKind;

    public bool $xMsIsHnsEnabled;

    public DateTimeImmutable $date;

    /**
     * Undocumented function
     *
     * @param array{
     *  Server: ?string,
     *  x-ms-request-id: ?string,
     *  x-ms-version: ?string,
     *  x-ms-sku-name: ?string,
     *  x-ms-account-kind: ?string,
     *  x-ms-is-hns-enabled: ?bool,
     *  Date: ?string
     * } $accountInformation
     */
    public function __construct(array $accountInformation)
    {
        $this->server          = $accountInformation['Server'] ?? '';
        $this->xMsRequestId    = $accountInformation['x-ms-request-id'] ?? '';
        $this->xMsVersion      = $accountInformation['x-ms-version'] ?? '';
        $this->xMsSkuName      = $accountInformation['x-ms-sku-name'] ?? '';
        $this->xMsAccountKind  = $accountInformation['x-ms-account-kind'] ?? '';
        $this->xMsIsHnsEnabled = to_boolean($accountInformation['x-ms-is-hns-enabled'] ?? false);
        $this->date            = new DateTimeImmutable($accountInformation['Date'] ?? 'now');
    }
}
