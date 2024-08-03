<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Contracts\Authentication;

use Xray\AzureStoragePhpSdk\Contracts\Http\Request;

interface Auth
{
    public function getDate(): string;

    public function getAccount(): string;

    public function getAuthentication(Request $request): string;
}
