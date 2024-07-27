<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Tests\Http\Concerns;

use PHPUnit\Framework\Assert;

/** @internal */
trait HasAuthAssertions
{
    public function assertWithAuthentication(): static
    {
        Assert::assertTrue($this->shouldAuthenticate);

        return $this;
    }

    public function assertWithoutAuthentication(): static
    {
        Assert::assertFalse($this->shouldAuthenticate);

        return $this;
    }
}
