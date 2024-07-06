<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Account\BlobStorageProperty\Cors;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;

/**
 * @phpstan-type CorsRuleType array{AllowedOrigins?: string, AllowedMethods?: string, MaxAgeInSeconds?: int, ExposedHeaders?: string, AllowedHeaders?: string}
 *
 * @implements Arrayable<array{CorsRule: CorsRuleType}>
 */
final readonly class CorsRule implements Arrayable
{
    /** @var array<string> $allowedOrigins */
    public array $allowedOrigins;

    /** @var array<string> $allowedMethods */
    public array $allowedMethods;

    public ?int $maxAgeInSeconds;

    /** @var array<string> $exposedHeaders */
    public array $exposedHeaders;

    /** @var array<string> $allowedHeaders */
    public array $allowedHeaders;

    /** @param CorsRuleType $corsRule */
    public function __construct(array $corsRule)
    {
        $this->allowedOrigins  = $this->parseCommaSeparatedList($corsRule['AllowedOrigins'] ?? '');
        $this->allowedMethods  = $this->parseCommaSeparatedList($corsRule['AllowedMethods'] ?? '');
        $this->maxAgeInSeconds = isset($corsRule['MaxAgeInSeconds']) ? (int) $corsRule['MaxAgeInSeconds'] : null;
        $this->exposedHeaders  = $this->parseCommaSeparatedList($corsRule['ExposedHeaders'] ?? '');
        $this->allowedHeaders  = $this->parseCommaSeparatedList($corsRule['AllowedHeaders'] ?? '');
    }

    public function toArray(): array
    {
        return [
            'CorsRule' => array_filter([
                'AllowedOrigins'  => implode(',', $this->allowedOrigins),
                'AllowedMethods'  => implode(',', $this->allowedMethods),
                'MaxAgeInSeconds' => $this->maxAgeInSeconds,
                'ExposedHeaders'  => implode(',', $this->exposedHeaders),
                'AllowedHeaders'  => implode(',', $this->allowedHeaders),
            ], fn (string|int|null $value) => $value !== null && $value !== ''),
        ];
    }

    /**
     * @param string $string
     * @return array<string>
     */
    protected function parseCommaSeparatedList(string $string): array
    {
        if (empty(trim($string))) {
            return [];
        }

        return explode(',', $string);
    }
}
