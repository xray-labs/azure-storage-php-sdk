<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\Cors;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;
use Sjpereira\AzureStoragePhpSdk\Support\Collection;

/**
 * @phpstan-import-type CorsRuleType from CorsRule
 * @phpstan-type CorsType CorsRuleType[]
 *
 * @extends Collection<int, CorsRule>
 * @implements Arrayable<array{Cors: CorsType}>
 */
final class Cors extends Collection implements Arrayable
{
    /** @param CorsRuleType|CorsRuleType[] $corsRules */
    public function __construct(array $corsRules)
    {
        $firstKey = array_keys($corsRules)[0] ?? null;

        if (isset($firstKey) && is_string($firstKey)) {
            $corsRules = [$corsRules];
        }

        /** @var CorsRuleType[] $corsRules */
        parent::__construct($this->generateCorsList($corsRules));
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

    /**
     * @param CorsRuleType[] $corsRules
     * @return CorsRule[]
     */
    protected function generateCorsList(array $corsRules): array
    {
        return array_map(
            fn (array $rule): CorsRule => new CorsRule($rule),
            $corsRules,
        );
    }
}
