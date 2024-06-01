<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Exceptions;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Throwable;

class RequestException extends Exception
{
    protected function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createFromRequestException(RequestExceptionInterface $exception): self
    {
        return new self($exception->getMessage(), $exception->getCode(), $exception);
    }
}
