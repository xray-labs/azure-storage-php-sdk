<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

final readonly class CorsRules
{
    public array $allowedOrigins;

    public array $allowedMethods;

    public ?int $maxAgeInSeconds;

    public array $exposedHeaders;

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

    protected function parseCommaSeparatedList(string $string): array
    {
        if (empty(trim($string))) {
            return [];
        }

        return explode(',', $string);
    }
}
