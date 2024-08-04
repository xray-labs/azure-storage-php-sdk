<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\Cors;

use Xray\AzureStoragePhpSdk\Contracts\Arrayable;
use Xray\AzureStoragePhpSdk\Support\Collection;

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
    public function __construct(array $corsRules = [])
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
            fn (array $rule): CorsRule => azure_app(CorsRule::class, ['corsRule' => $rule]),
            $corsRules,
        );
    }
}
