<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob;

use Psr\Http\Client\RequestExceptionInterface;
use Xray\AzureStoragePhpSdk\BlobStorage\Enums\BlobType;
use Xray\AzureStoragePhpSdk\BlobStorage\Resource;
use Xray\AzureStoragePhpSdk\BlobStorage\Resources\File;
use Xray\AzureStoragePhpSdk\Concerns\HasManager;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\{InvalidArgumentException, RequestException};

class BlobPageManager implements Manager
{
    /** @use HasManager<BlobManager> */
    use HasManager;

    public const int PAGE_SIZE_BYTES = 512;

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
                    Resource::BLOB_TYPE           => BlobType::PAGE->value,
                    Resource::BLOB_CONTENT_LENGTH => $length,
                ], $headers))
                ->put("{$this->containerName}/{$name}?resttype=blob")
                ->isCreated();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /** @param array<string, scalar> $options */
    public function append(File $file, int $startPage, ?int $endPage = null, array $options = []): bool
    {
        $this->validatePageBytesBoundary($file->contentLength);

        ['startByte' => $startByte] = $this->getPageRange($startPage);

        $endByte = $startByte + $file->contentLength - 1;

        if ($endPage) {
            ['endByte' => $endByte] = $this->getPageRange($endPage);
        }

        $this->validatePageSize($startByte, $endByte, $file->contentLength);

        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::PAGE_WRITE     => 'update',
                    Resource::RANGE          => "bytes={$startByte}-{$endByte}",
                    Resource::CONTENT_TYPE   => $file->contentType,
                    Resource::CONTENT_LENGTH => $file->contentLength,
                    Resource::CONTENT_MD5    => $file->contentMD5,
                ])
                ->put("{$this->containerName}/{$file->name}?resttype=blob&comp=page", $file->content)
                ->isCreated();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /** @param array<string, scalar> $options */
    public function put(File $file, array $options = []): bool
    {
        $this->validatePageBytesBoundary($file->contentLength);

        try {
            $this->create($file->name, $file->contentLength, $options, [
                Resource::CONTENT_TYPE => $file->contentType,
                Resource::CONTENT_MD5  => $file->contentMD5,
            ]);

            return $this->append($file, 1, options: $options);

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /** @param array<string, scalar> $options */
    public function clear(string $name, int $startPage = 1, ?int $endPage = null, array $options = []): bool
    {
        ['startByte' => $startByte, 'endByte' => $endByte] = $this->getPageRange($startPage);

        if ($endPage) {
            ['endByte' => $endByte] = $this->getPageRange($endPage);
        }

        $this->validatePageSize($startByte, $endByte);

        try {
            return $this->request
                ->withOptions($options)
                ->withHeaders([
                    Resource::PAGE_WRITE => 'clear',
                    Resource::RANGE      => "bytes={$startByte}-{$endByte}",
                ])
                ->put("{$this->containerName}/{$name}?resttype=blob&comp=page")
                ->isCreated();

            // @codeCoverageIgnoreStart
        } catch (RequestExceptionInterface $e) {
            throw RequestException::createFromRequestException($e);
        }
        // @codeCoverageIgnoreEnd
    }

    /** @param array<string, scalar> $options */
    public function clearAll(string $name, array $options = []): bool
    {
        $this->ensureManagerIsConfigured();

        $file = $this->getManager()->get($name);

        return $this->clear($name, 1, (int)($file->contentLength / self::PAGE_SIZE_BYTES), $options);
    }

    /** @return array{startByte: int, endByte: int} */
    private function getPageRange(int $page): array
    {
        $endByte   = $page * self::PAGE_SIZE_BYTES - 1;
        $startByte = $endByte - self::PAGE_SIZE_BYTES + 1;

        return [
            'startByte' => $startByte,
            'endByte'   => $endByte,
        ];
    }

    private function validatePageSize(int $startByte, int $endByte, ?int $fileSize = null): void
    {
        if ($endByte < self::PAGE_SIZE_BYTES - 1 || $startByte < 0) {
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
        if ($length % self::PAGE_SIZE_BYTES !== 0) {
            throw InvalidArgumentException::create('Page blob size must be aligned to a 512-byte boundary.');
        }
    }
}
