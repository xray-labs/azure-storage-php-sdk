<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Application\Application;

if (!function_exists('azure_app')) {
    /**
     * Get the available container instance.
     *
     * @template TClass
     *
     * @param string|class-string<TClass>|null $key
     * @param array<string, mixed> $parameters
     * @return ($key is class-string<TClass> ? TClass : ($key is null ? Application : mixed))
     */
    function azure_app(?string $key = null, array $parameters = []): mixed
    {
        $instance = Application::getInstance();

        if (is_null($key)) {
            return $instance;
        }

        return $instance->make($key, $parameters);
    }
}
