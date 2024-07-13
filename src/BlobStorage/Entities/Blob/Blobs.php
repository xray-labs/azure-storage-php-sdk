<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Sjpereira\AzureStoragePhpSdk\Support\Collection;

/**
 * @phpstan-import-type BlobType from Blob
 *
 * @extends Collection<int, Blob>
 */
final class Blobs extends Collection
{
    /** @param BlobType|BlobType[] $blobs */
    public function __construct(protected BlobManager $manager, array $blobs = [])
    {
        if (is_string(array_keys($blobs)[0])) {
            $blobs = [$blobs];
        }

        /** @var BlobType[] $blobs */
        parent::__construct($this->generateBlobsList($blobs));
    }

    /**
     * @param BlobType[] $blobs
     * @return Blob[]
     */
    protected function generateBlobsList(array $blobs): array
    {
        return array_map(
            fn (array $blob) => (new Blob($blob))->setManager($this->manager),
            $blobs,
        );
    }
}
