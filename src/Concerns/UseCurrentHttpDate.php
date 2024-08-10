<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Concerns;

trait UseCurrentHttpDate
{
    public function getDate(): string
    {
        return gmdate('D, d M Y H:i:s T');
    }
}
