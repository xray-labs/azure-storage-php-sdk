<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Fakes\Http\Concerns;

use PHPUnit\Framework\Assert;

/** @internal */
trait HasHttpAssertions
{
    /** @param array<string, scalar> $options */
    public function assertSentWithOptions(array $options): static
    {
        Assert::assertSame($options, $this->options);

        return $this;
    }

    /** @param array<string, scalar> $headers */
    public function assertSentWithHeaders(array $headers): static
    {
        Assert::assertSame($headers, $this->headers);

        return $this;
    }

    public function assertUsingAccount(string $account): static
    {
        Assert::assertIsCallable($this->usingAccountCallback, 'Account callback not set');

        $value = call_user_func($this->usingAccountCallback, $this->getAuth()->getAccount());
        Assert::assertSame($account, $value);

        return $this;
    }

    public function assertGet(string $endpoint): static
    {
        $this->assertMethod('get', $endpoint);

        return $this;
    }

    public function assertPost(string $endpoint, ?string $body = null): static
    {
        $this->assertMethod('post', $endpoint);

        if (!is_null($body)) {
            Assert::assertSame($body, $this->methods['post']['body'] ?? '');
        }

        return $this;
    }

    public function assertPut(string $endpoint, ?string $body = null): static
    {
        $this->assertMethod('put', $endpoint);

        if (!is_null($body)) {
            Assert::assertSame($body, $this->methods['put']['body'] ?? '');
        }

        return $this;
    }

    public function assertDelete(string $endpoint): static
    {
        $this->assertMethod('delete', $endpoint);

        return $this;
    }

    public function assertOptions(string $endpoint): static
    {
        $this->assertMethod('options', $endpoint);

        return $this;
    }

    protected function assertMethod(string $method, string $endpoint): void
    {
        Assert::assertArrayHasKey($method, $this->methods);
        Assert::assertSame($endpoint, $this->methods[$method]['endpoint'] ?? '');
    }
}
