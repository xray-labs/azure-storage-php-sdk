<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Enums;

enum HttpVerb: string
{
    case GET     = 'GET';
    case PUT     = 'PUT';
    case POST    = 'POST';
    case DELETE  = 'DELETE';
    case HEAD    = 'HEAD';
    case OPTIONS = 'OPTIONS';
    case MERGE   = 'MERGE';
    case PATCH   = 'PATCH';
}
