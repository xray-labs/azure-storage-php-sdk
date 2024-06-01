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
    public function __construct(array $corsRules)
    {
        if (isset(array_keys($corsRules)[0]) && is_string(array_keys($corsRules)[0])) {
            $corsRules = [$corsRules];
        }

        parent::__construct(
            array_map(
                fn (array $rule) => new CorsRule($rule),
                $corsRules,
            ),
        );
    }

    public function toArray(): array
    {
        return [
            'Cors' => array_map(
                fn (CorsRule $rule) => $rule->toArray(),
                $this->all(),
            ),
        ];
    }
}
