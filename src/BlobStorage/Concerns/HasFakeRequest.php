<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Concerns;

use Xray\AzureStoragePhpSdk\BlobStorage\Config;
use Xray\AzureStoragePhpSdk\Contracts\Authentication\Auth;
use Xray\AzureStoragePhpSdk\Contracts\{Converter, Parser};
use Xray\AzureStoragePhpSdk\Fakes\Http\RequestFake;

trait HasFakeRequest
{
    /** @param array{version?: string, parser?: Parser, converter?: Converter} $config */
    public static function fake(?Auth $auth = null, array $config = []): static
    {
        /** @phpstan-ignore-next-line */
        return new static(new RequestFake($auth, new Config($config)));
    }
}
