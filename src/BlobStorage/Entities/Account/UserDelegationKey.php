<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account;

use DateTimeImmutable;

final readonly class UserDelegationKey
{
    public string $signedOid;

    public string $signedTid;

    public DateTimeImmutable $signedStart;

    public DateTimeImmutable $signedExpiry;

    public string $signedService;

    public string $signedVersion;

    public string $value;

    /** @param array{SignedOid: string, SignedTid: string, SignedStart: string, SignedExpiry: string, SignedService: string, SignedVersion: string, Value: string} $userDelegationKey */
    public function __construct(array $userDelegationKey)
    {
        $this->signedOid     = $userDelegationKey['SignedOid'];
        $this->signedTid     = $userDelegationKey['SignedTid'];
        $this->signedStart   = new DateTimeImmutable($userDelegationKey['SignedStart']);
        $this->signedExpiry  = new DateTimeImmutable($userDelegationKey['SignedExpiry']);
        $this->signedService = $userDelegationKey['SignedService'];
        $this->signedVersion = $userDelegationKey['SignedVersion'];
        $this->value         = $userDelegationKey['Value'];
    }
}
