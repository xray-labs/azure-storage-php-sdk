<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Container\AccessLevel;

use Xray\AzureStoragePhpSdk\BlobStorage\Managers\Container\ContainerAccessLevelManager;
use Xray\AzureStoragePhpSdk\Support\Collection;

/**
 * @method array<ContainerAccessLevel> all()
 * @method ?ContainerAccessLevel first()
 * @method ?ContainerAccessLevel last()
 * @method ?ContainerAccessLevel get(int $key)
 *
 * @extends Collection<int, ContainerAccessLevel>
*/
final class ContainerAccessLevels extends Collection
{
    /** @param array<array<mixed>> $levels */
    public function __construct(protected ContainerAccessLevelManager $manager, array $levels = [])
    {
        if (is_string(array_keys($levels)[0])) {
            $levels = [$levels];
        }

        parent::__construct(array_map(
            [$this, 'mapContainerAccessLevel'],
            $levels,
        ));
    }

    /**
     * @param array{
     *   Id: ?string,
     *   AccessPolicy: ?array{
     *     Start: string,
     *     Expiry: string,
     *     Permission: string
     *   }
     *  } $level
    */
    protected function mapContainerAccessLevel(array $level): ContainerAccessLevel
    {
        return new ContainerAccessLevel($level);
    }
}
