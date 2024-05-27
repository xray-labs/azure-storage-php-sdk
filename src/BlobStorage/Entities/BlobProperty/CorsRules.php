<?php

declare(strict_types=1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\BlobProperty;

readonly class CorsRules
{
    public array $allowedOrigins;

    public array $allowedMethods;

    public int $maxAgeInSeconds;

    public array $exposedHeaders;

    public array $allowedHeaders;

    public function __construct(array $corsRules)
    {
        $this->allowedOrigins  = explode(',', $corsRules['AllowedOrigins'] ?? '');
        $this->allowedMethods  = explode(',', $corsRules['AllowedMethods'] ?? '');
        $this->maxAgeInSeconds = (int) ($corsRules['MaxAgeInSeconds'] ?? 0);
        $this->exposedHeaders  = explode(',', $corsRules['ExposedHeaders'] ?? '');
        $this->allowedHeaders  = explode(',', $corsRules['AllowedHeaders'] ?? '');
    }
}
