<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Exceptions;

use Exception;

final class RequiredFieldException extends Exception
{
    protected function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function missingField(string $field): self
    {
        return new self("Field [{$field}] is required");
    }
}
