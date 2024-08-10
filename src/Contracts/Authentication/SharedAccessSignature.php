<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Authentication;

use DateTimeImmutable;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\AccessTokenPermission;

interface SharedAccessSignature
{
    public function buildTokenUrl(AccessTokenPermission $permission, DateTimeImmutable $expiry): string;
}
