<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Enums;

enum HttpVerb: string
{
    case DELETE  = 'DELETE';
    case GET     = 'GET';
    case HEAD    = 'HEAD';
    case MERGE   = 'MERGE';
    case POST    = 'POST';
    case OPTIONS = 'OPTIONS';
    case PUT     = 'PUT';
    case PATCH   = 'PATCH';
}
