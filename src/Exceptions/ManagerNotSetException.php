<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions;

use Exception;

class ManagerNotSetException extends Exception
{
    protected function __construct(string $message)
    {
        parent::__construct($message);
    }

    public static function create(): self
    {
        return new self('Manager not set');
    }
}
