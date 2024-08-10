<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk\Application;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionNamedType;
use ReflectionParameter;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

class Application
{
    /**
     * The application singleton instance.
     *
     * @var self $instance
     */
    protected static self $instance;

    /**
     * All instances registered in the container.
     *
     * @var array<string, object> $instances
     */
    protected array $instances = [];

    /**
     * All bindings registered in the container.
     *
     * @var array<string, array{callback: callable, shared: bool}> $bindings
     */
    protected array $bindings = [];

    /**
     * All scope instances registered in the container.
     *
     * @var array<string, object> $scopeInstances
     */
    protected array $scopeInstances = [];

    /**
     * All scope methods registered in the container.
     *
     * @var array<string, callable> $scopeMethods
     */
    protected array $scopeMethods = [];

    /**
     * Create a new application instance.
     */
    public function __construct()
    {
        $this->instance(self::class, $this);
    }

    /**
     * Get the application singleton instance.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Bind an instance to the container.
     *
     * @template TInstance of object
     *
     * @param string $key
     * @param TInstance $instance
     * @return self
     */
    public function instance(string $key, object $instance): self
    {
        $this->instances[$key] = $instance;

        return $this;
    }

    /**
     * Bind a singleton callback to the container.
     *
     * @param string|class-string $key
     * @param callable|null $callback
     * @return self
     */
    public function singleton(string $key, ?callable $callback = null): self
    {
        unset($this->instances[$key]);
        $this->bind($key, $callback, shared: true);

        return $this;
    }

    /**
     * Bind a callback to the container.
     *
     * @param string|class-string $key
     * @param callable|null $callback
     * @param bool $shared
     * @return self
     */
    public function bind(string $key, ?callable $callback = null, bool $shared = false): self
    {
        if (is_null($callback) && !class_exists($key)) {
            throw InvalidArgumentException::create("Cannot bind {$key} without a callback");
        }

        if (is_null($callback)) {
            /** @var class-string $key */
            $callback = fn () => $this->build($key);
        }

        $this->bindings[$key] = [
            'callback' => $callback,
            'shared'   => $shared,
        ];

        return $this;
    }

    /**
     * Bind a scoped callback to the container.
     *
     * @param string|class-string $key
     * @param callable|null $callback
     * @return self
     */
    public function scope(string $key, ?callable $callback = null): self
    {
        if (is_null($callback) && !class_exists($key)) {
            throw InvalidArgumentException::create("Cannot scope {$key} without a callback");
        }

        if (is_null($callback)) {
            /** @var class-string $key */
            $callback = fn () => $this->build($key);
        }

        unset($this->scopeInstances[$key]);
        $this->scopeMethods[$key] = $callback;

        return $this;
    }

    /**
     * Determine if the container has a given instance or binding.
     *
     * @param string $key
     * @return boolean
     */
    public function bound(string $key): bool
    {
        return isset($this->instances[$key])
            || isset($this->bindings[$key])
            || isset($this->scopeInstances[$key])
            || isset($this->scopeMethods[$key]);
    }

    /**
     * Resolve an instance from the container.
     *
     * @template TClass of object
     *
     * @param string|class-string<TClass> $key
     * @param array<string, mixed> $parameters
     * @return ($key is class-string<TClass> ? TClass : mixed)
     */
    public function make(string $key, array $parameters = []): mixed
    {
        if (isset($this->instances[$key]) || isset($this->scopeInstances[$key])) {
            return $this->instances[$key] ?? $this->scopeInstances[$key];
        }

        if (isset($this->bindings[$key]) || isset($this->scopeMethods[$key])) {
            return $this->resolveBinding($key);
        }

        if (!class_exists($key)) {
            throw InvalidArgumentException::create("Cannot resolve class {$key}");
        }

        /** @var class-string<TClass> $key */
        return $this->build($key, $parameters);
    }

    /**
     * Resolve a callback with the container.
     *
     * @param callable $callback
     * @param array<mixed> $parameters
     * @return mixed
     */
    public function call(callable $callback, array $parameters = []): mixed
    {
        $reflection   = new ReflectionFunction(Closure::fromCallable($callback));
        $dependencies = $this->resolveDependencies($reflection->getParameters(), $parameters);

        return call_user_func_array($reflection->getClosure(), $dependencies);
    }

    /**
     * Flush the container of all bindings and resolved instances.
     *
     * @return self
     */
    public function flush(): self
    {
        $this->instances      = [];
        $this->bindings       = [];
        $this->scopeInstances = [];
        $this->scopeMethods   = [];

        return $this;
    }

    /**
     * Flush the container of all scoped bindings and scoped instances.
     *
     * @return self
     */
    public function flushScoped(): self
    {
        $this->scopeInstances = [];
        $this->scopeMethods   = [];

        return $this;
    }

    /**
     * Resolve an instance binding from the container.
     *
     * @param string $key
     * @return mixed
     */
    protected function resolveBinding(string $key): mixed
    {
        /**
         * @var callable $callback
         * @var bool $shared
         * @var bool $scoped
         */
        [$callback, $shared, $scoped] = isset($this->bindings[$key])
            ? [...array_values($this->bindings[$key]), false]
            : [$this->scopeMethods[$key], false, true];

        /** @var object $concrete */
        $concrete = $this->call($callback);

        if ($shared && !$scoped) {
            $this->instances[$key] = $concrete;
            unset($this->bindings[$key]);
        }

        if ($scoped) {
            $this->scopeInstances[$key] = $concrete;
            unset($this->scopeMethods[$key]);
        }

        return $concrete;
    }

    /**
     * Build an instance from the container.
     *
     * @template TClass of object
     *
     * @param class-string<TClass> $key
     * @param array<string, mixed> $parameters
     * @return TClass
     */
    protected function build(string $key, array $parameters = []): object
    {
        $reflection  = new ReflectionClass($key);
        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return $reflection->newInstance();
        }

        $dependencies = $this->resolveDependencies($constructor->getParameters(), $parameters);

        return $reflection->newInstanceArgs($dependencies);
    }

    /**
     * Build an array of dependencies from the container.
     *
     * @param \ReflectionParameter[] $dependencies
     * @param array<string, mixed> $parameters
     * @return array<mixed>
     */
    protected function resolveDependencies(array $dependencies, array $parameters): array
    {
        return array_map(function (ReflectionParameter $dependency) use ($parameters): mixed {
            $name = $dependency->getName();

            if (array_key_exists($name, $parameters)) {
                return $parameters[$name];
            }

            if ($dependency->isDefaultValueAvailable()) {
                return $dependency->getDefaultValue();
            }

            $type = $dependency->getType();

            if (is_null($type) || !$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                throw InvalidArgumentException::create("Cannot resolve parameter \${$name} without one defined type");
            }

            return $this->make($type->getName(), $parameters);
        }, $dependencies);
    }
}
