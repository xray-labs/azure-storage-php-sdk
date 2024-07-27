<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use Xray\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Xray\AzureStoragePhpSdk\Support\Collection;

/**
 * @phpstan-import-type ContainerType from Container
 *
 * @extends Collection<int, Container>
 */
final class Containers extends Collection
{
    /** @param ContainerType|ContainerType[] $containers */
    public function __construct(protected ContainerManager $manager, array $containers = [])
    {
        if (is_string(array_keys($containers)[0])) {
            $containers = [$containers]; // @codeCoverageIgnore
        }

        /** @var ContainerType[] $containers */
        parent::__construct($this->generateContainersList($containers));
    }

    /**
     * @param ContainerType[] $containers
     * @return Container[]
     */
    protected function generateContainersList(array $containers): array
    {
        return array_map(
            fn (array $container) => (new Container($container))->setManager($this->manager),
            $containers,
        );
    }
}
