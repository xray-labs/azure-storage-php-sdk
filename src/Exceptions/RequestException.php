<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\Exceptions;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\Exceptions\RequestException\FailedAuthenticationException;
use Throwable;

class RequestException extends Exception
{
    protected function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createFromRequestException(RequestExceptionInterface $exception): static
    {
        // TODO: Customize Exception Depending on the status code

        return match($exception->getCode()) {
            403 => new FailedAuthenticationException($exception->getMessage(), $exception->getCode(), $exception),
            default => new static($exception->getMessage(), $exception->getCode(), $exception),
        };
    }
}