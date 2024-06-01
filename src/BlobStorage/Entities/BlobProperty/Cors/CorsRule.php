<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty\Cors;

use Sjpereira\AzureStoragePhpSdk\Contracts\Arrayable;

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

    /**
     * @param array{
     *     AllowedOrigins?: string,
     *     AllowedMethods?: string,
     *     MaxAgeInSeconds?: int,
     *     ExposedHeaders?: string,
     *     AllowedHeaders?: string,
     * } $corsRules
     */
    public function __construct(array $corsRules)
    {
        $this->allowedOrigins  = $this->parseCommaSeparatedList($corsRules['AllowedOrigins'] ?? '');
        $this->allowedMethods  = $this->parseCommaSeparatedList($corsRules['AllowedMethods'] ?? '');
        $this->maxAgeInSeconds = isset($corsRules['MaxAgeInSeconds']) ? (int) $corsRules['MaxAgeInSeconds'] : null;
        $this->exposedHeaders  = $this->parseCommaSeparatedList($corsRules['ExposedHeaders'] ?? '');
        $this->allowedHeaders  = $this->parseCommaSeparatedList($corsRules['AllowedHeaders'] ?? '');
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
