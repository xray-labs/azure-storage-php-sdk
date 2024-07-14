<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums;

enum ExpirationOption: string
{
    case RELATIVE_TO_CREATION = 'RelativeToCreation';
    case RELATIVE_T0_NOW      = 'RelativeToNow';
    case ABSOLUTE             = 'Absolute';
    case NEVER_EXPIRE         = 'NeverExpire';
}
