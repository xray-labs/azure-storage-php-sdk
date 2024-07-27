<?php
declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Concerns;

use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

trait HasStreamingResponse
{
    public static function stream(File $file, ?int $expires = null): string
    {
        static::validateExpiresResponse($expires);

        return static::handleResponseStreaming('inline', $file, $expires ?? 0);
    }

    public static function download(File $file, ?int $expires = null): string
    {
        static::validateExpiresResponse($expires);

        return static::handleResponseStreaming('attachment', $file, $expires ?? 0);
    }

    protected static function handleResponseStreaming(string $disposition, File $file, int $expires): string
    {
        header("Content-Disposition: {$disposition}; filename=\"{$file->getFilename()}\"");
        header("Content-Type: {$file->getContentType()}");
        header("Content-Length: {$file->getContentLength()}");
        header('Cache-Control: no-cache, must-revalidate');
        header("Expires: {$expires}");

        return with($file->getContent(), function (string $content): void {
            if (!is_running_in_console()) {
                echo $content; // @codeCoverageIgnore
            }
        });
    }

    /** @phpstan-assert null|positive-int $expires */
    protected static function validateExpiresResponse(?int $expires): void
    {
        if (is_int($expires) && $expires < 0) {
            throw InvalidArgumentException::create('Expires cannot be less than 0.');
        }
    }
}
