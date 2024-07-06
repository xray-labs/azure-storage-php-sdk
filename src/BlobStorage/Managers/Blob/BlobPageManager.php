<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob\{Blob, File};
use Sjpereira\AzureStoragePhpSdk\Concerns\HasManager;
use Sjpereira\AzureStoragePhpSdk\Contracts\Http\Request;
use Sjpereira\AzureStoragePhpSdk\Contracts\Manager;
use Sjpereira\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequestException};

/**
 * @phpstan-import-type BlobType from Blob
 * @phpstan-import-type FileType from File
 */
class BlobPageManager implements Manager
{
    /** @use HasManager<BlobManager> */
    use HasManager;

    public const int PAGE_SIZE = 512;

    public function __construct(protected readonly Request $request, protected readonly string $containerName)
    {
        //
    }

    /**
     * @param array<string, scalar> $options
     * @param array<string, scalar> $headers
     */
    public function create(string $name, int $length, array $options = [], array $headers = []): bool
    {
        $this->validatePageBytesBoundary($length);

        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders(array_merge([
                    'x-ms-blob-type'           => 'PageBlob',
                    'x-ms-blob-content-length' => $length,
                ], $headers))
                ->put("{$this->containerName}/{$name}?resttype=blob")
                ->isCreated();
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    /** @param array<string, scalar> $options */
    public function append(File $file, int $startPage, ?int $endPage = null, array $options = []): void
    {
        $this->validatePageBytesBoundary($file->contentLength);

        ['startByte' => $startByte] = $this->getPageRange($startPage);

        $endByte = $startByte + $file->contentLength - 1;

        if ($endPage) {
            ['endByte' => $endByte] = $this->getPageRange($endPage);
        }

        $this->validatePageSize($startByte, $endByte, $file->contentLength);

        try {
            $this->request
                ->withOptions($options)
                ->withHeaders([
                    'x-ms-page-write' => 'update',
                    'x-ms-range'      => "bytes={$startByte}-{$endByte}",
                    'Content-Type'    => $file->contentType,
                    'Content-Length'  => $file->contentLength,
                    'Content-MD5'     => $file->contentMD5,
                ])
                ->put("{$this->containerName}/{$file->name}?resttype=blob&comp=page", $file->content);
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    /** @param array<string, scalar> $options */
    public function put(File $file, array $options = []): void
    {
        $this->validatePageBytesBoundary($file->contentLength);

        try {
            $this->create($file->name, $file->contentLength, $options, [
                'Content-Type' => $file->contentType,
                'Content-MD5'  => $file->contentMD5,
            ]);

            $this->append($file, 1, options: $options);
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    /** @param array<string, scalar> $options */
    public function clear(string $name, int $startPage = 1, ?int $endPage = null, array $options = []): void
    {
        ['startByte' => $startByte, 'endByte' => $endByte] = $this->getPageRange($startPage);

        if ($endPage) {
            ['endByte' => $endByte] = $this->getPageRange($endPage);
        }

        $this->validatePageSize($startByte, $endByte);

        try {
            $this->request
                ->withOptions($options)
                ->withHeaders([
                    'x-ms-page-write' => 'clear',
                    'x-ms-range'      => "bytes={$startByte}-{$endByte}",
                ])
                ->put("{$this->containerName}/{$name}?resttype=blob&comp=page");
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
    }

    /** @param array<string, scalar> $options */
    public function clearAll(string $name, array $options = []): void
    {
        $this->ensureManagerIsConfigured();

        $file = $this->getManager()->get($name);

        $this->clear($name, 1, (int)($file->contentLength / self::PAGE_SIZE), $options);
    }

    /** @return array{startByte: int, endByte: int} */
    private function getPageRange(int $page): array
    {
        $endByte   = $page * self::PAGE_SIZE - 1;
        $startByte = $endByte - self::PAGE_SIZE + 1;

        return [
            'startByte' => $startByte,
            'endByte'   => $endByte,
        ];
    }

    private function validatePageSize(int $startByte, int $endByte, ?int $fileSize = null): void
    {
        if ($endByte < self::PAGE_SIZE - 1 || $startByte < 0) {
            throw InvalidArgumentException::create('The start page should be greater than 0');
        }

        if ($startByte > $endByte) {
            throw InvalidArgumentException::create('The end page should be greater than the start page');
        }

        if ($fileSize && $fileSize > $endByte - $startByte + 1) {
            throw InvalidArgumentException::create('The file size is greater than the page range');
        }
    }

    private function validatePageBytesBoundary(int $length): void
    {
        if ($length % self::PAGE_SIZE !== 0) {
            throw InvalidArgumentException::create('Page blob size must be aligned to a 512-byte boundary.');
        }
    }
}
