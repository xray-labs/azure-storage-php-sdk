<?php

namespace Xray\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Xray\AzureStoragePhpSdk\Contracts\Http\Request;
use Xray\AzureStoragePhpSdk\Tests\Http\RequestFake;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        azure_app()->instance(Request::class, new RequestFake());
    }
}
