<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

use Sjpereira\AzureStoragePhpSdk\BlobStorage\Managers\ContainerManager;
use Sjpereira\AzureStoragePhpSdk\Support\Collection;

/**
 * @method array<Container> all()
 * @method ?Container first()
 * @method ?Container last()
 * @method ?Container get(int $key)
 *
 * @extends Collection<int, Container>
*/
final class Containers extends Collection
{
    /**
     * Undocumented function
     *
     * @param ContainerManager $manager
     * @param array<array<mixed>> $containers
     */
    public function __construct(protected ContainerManager $manager, array $containers = [])
    {
        if (is_string(array_keys($containers)[0])) {
            $containers = [$containers];
        }

        parent::__construct(array_map(
            fn (array $container) => new Container($manager, $container),
            $containers,
        ));
    }
}
