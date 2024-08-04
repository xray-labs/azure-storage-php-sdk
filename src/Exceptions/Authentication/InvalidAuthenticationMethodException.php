<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions\Authentication;

use Exception;

class InvalidAuthenticationMethodException extends Exception
{
    public static function create(string $message): self
    {
        return new self($message);
    }
}
