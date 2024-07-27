<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Http;

interface HttpMethods
{
    public function get(string $endpoint): Response;

    public function post(string $endpoint, string $body = ''): Response;

    public function put(string $endpoint, string $body = ''): Response;

    public function delete(string $endpoint): Response;

    public function options(string $endpoint): Response;
}
