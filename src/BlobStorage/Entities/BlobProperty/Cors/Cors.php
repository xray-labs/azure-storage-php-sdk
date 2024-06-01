<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty\Cors;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;
use Sjpereira\AzureStoragePhpSdk\Support\Collection;

/**
 * @method array<CorsRule> all()
 * @method ?CorsRule first()
 * @method ?CorsRule last()
 * @method ?CorsRule get(int $key)
 *
 * @extends Collection<int, CorsRule>
 */
class Cors extends Collection implements Arrayable
{
    public function __construct()
    {
        //
    }

    public function toArray(): array
    {
        return [
            'Cors' => [
                array_map(
                    fn (CorsRule $rule) => $rule->toArray(),
                    $this->all(),
                ),
            ],
        ];
    }
}
