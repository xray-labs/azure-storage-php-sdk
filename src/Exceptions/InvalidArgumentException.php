<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Exceptions;

use Exception;

class InvalidArgumentException extends Exception
{
    protected function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(string $message): self
    {
        return new self($message);
    }
}
