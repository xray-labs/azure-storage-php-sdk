<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums;

enum HttpVerb: string
{
    case GET    = 'GET';
    case PUT    = 'PUT';
    case DELETE = 'DELETE';
}
