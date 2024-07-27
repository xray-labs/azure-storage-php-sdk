<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions;

use Exception;

class InvalidFileMimeTypeException extends Exception
{
    protected function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(string $message = 'The file mime type is invalid'): self
    {
        return new self($message);
    }
}
