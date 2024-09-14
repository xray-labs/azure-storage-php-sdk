<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions;

use Exception;

final class RequiredFieldException extends Exception
{
    public static function create(string $message): static
    {
        return new static($message);
    }

    public static function missingField(string $field): static
    {
        return new static("Field [{$field}] is required");
    }
}
