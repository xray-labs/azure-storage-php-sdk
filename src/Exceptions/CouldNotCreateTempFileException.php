<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions;

use Exception;

final class CouldNotCreateTempFileException extends Exception
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
