<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage;

class SignatureResource
{
    public const string SIGNED_PERMISSION = 'sp';

    public const string SIGNED_START = 'st';

    public const string SIGNED_EXPIRY = 'se';

    public const string SIGNED_CANONICAL_RESOURCE = 'scr';

    public const string SIGNED_OBJECT_ID = 'skoid';

    public const string SIGNED_TENANT_ID = 'sktid';

    public const string SIGNED_KEY_START_TIME = 'skt';

    public const string SIGNED_KEY_EXPIRY_TIME = 'ske';

    public const string SIGNED_KEY_SERVICE = 'sks';

    public const string SIGNED_KEY_VERSION = 'skv';

    public const string SIGNED_AUTHORIZED_OBJECT_ID = 'saoid';

    public const string SIGNED_UNAUTHORIZED_OBJECT_ID = 'suoid';

    public const string SIGNED_CORRELATION_ID = 'scid';

    public const string SIGNED_IP_ADDRESS = 'sip';

    public const string SIGNED_PROTOCOL = 'spr';

    public const string SIGNED_VERSION = 'sv';

    public const string SIGNED_RESOURCE = 'sr';

    public const string SIGNED_SNAPSHOT_TIME = 'sst';

    public const string SIGNED_ENCRYPTION_SCOPE = 'ses';

    public const string RESOURCE_CACHE_CONTROL = 'rscc';

    public const string RESOURCE_CONTENT_DISPOSITION = 'rscd';

    public const string RESOURCE_CONTENT_ENCODING = 'rsce';

    public const string RESOURCE_CONTENT_LANGUAGE = 'rscl';

    public const string RESOURCE_CONTENT_TYPE = 'rsct';

    public const string SIGNATURE = 'sig';
}
