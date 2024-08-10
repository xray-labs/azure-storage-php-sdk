<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Enums;

enum AccessTokenPermission: string
{
    case READ   = 'r';
    case WRITE  = 'w';
    case DELETE = 'd';
}
