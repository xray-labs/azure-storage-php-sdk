<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Blob;

use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Blob\BlobManager;
use Xray\AzureStoragePhpSdk\Support\Collection;

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
            fn (array $blob) => (azure_app(Blob::class, ['blob' => $blob]))->setManager($this->manager),
            $blobs,
        );
    }
}
