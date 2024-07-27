<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Exceptions;

use Exception;
use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\Exceptions\RequestException\FailedAuthenticationException;
use Throwable;

class RequestException extends Exception
{
    protected function __construct(string $message, int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public static function createFromRequestException(RequestExceptionInterface $exception): self
    {
        // FIX: Customize Exception Depending on the status code

        return match($exception->getCode()) {
            403     => new FailedAuthenticationException($exception->getMessage(), $exception->getCode(), $exception),
            default => new self($exception->getMessage(), $exception->getCode(), $exception),
        };
    }

    public static function createFromMessage(string $message): self
    {
        return new self($message);
    }
}
