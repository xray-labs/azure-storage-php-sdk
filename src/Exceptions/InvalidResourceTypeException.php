<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions;

use Exception;

final class InvalidResourceTypeException extends Exception
{
    public static function create(string $message): self
    {
        return new self($message);
    }
}
