<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums;

enum BlobType: string
{
    case BLOCK  = 'BlockBlob';
    case PAGE   = 'PageBlob';
    case APPEND = 'AppendBlob';
}
