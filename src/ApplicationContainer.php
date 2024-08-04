<?php

declare(strict_types=1);

namespace Xray\AzureStoragePhpSdk;

use Closure;
use ReflectionClass;
use ReflectionNamedType;
use ReflectionParameter;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

class ApplicationContainer
{
    protected static self $instance;

    /** @var array<class-string, Closure> $bindings */
    protected array $bindings = [];

    /** @var array<string, object> $instances */
    protected array $instances = [];

    public static function getContainer(): self
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
     * @param TInstance $instance
     */
    public function instance(string $abstract, object $instance): self
    {
        $this->instances[$abstract] = $instance;

        return $this;
    }

    /**
     * Bind a class to the container.
     *
     * @template TClass of object
     *
     * @param class-string<TClass> $abstract
     */
    public function bind(string $abstract, ?Closure $callback = null): self
    {
        if (is_null($callback) && !class_exists($abstract)) {
            throw InvalidArgumentException::create("Cannot bind abstract class $abstract without a callback");
        }

        if (is_null($callback)) {
            $callback = fn () => $this->build($abstract);
        }

        $this->bindings[$abstract] = $callback;

        return $this;
    }

    /** @param array<string, scalar|object|array<mixed>> $parameters */
    public function make(string $abstract, array $parameters = []): mixed
    {
        if (isset($this->instances[$abstract])) {
            return $this->instances[$abstract];
        }

        if (isset($this->bindings[$abstract])) {
            return $this->bindings[$abstract]($this);
        }

        if (!class_exists($abstract)) {
            throw InvalidArgumentException::create("Cannot resolve class {$abstract}");
        }

        return $this->build($abstract, $parameters);
    }

    public function flush(): self
    {
        $this->bindings  = [];
        $this->instances = [];

        return $this;
    }

    /** @param array<string, scalar|object|array<mixed>> $arguments */
    protected function build(string $abstract, array $arguments = []): object
    {
        /** @var class-string $abstract */
        $reflection = new ReflectionClass($abstract);

        $constructor = $reflection->getConstructor();

        if (is_null($constructor)) {
            return $reflection->newInstance();
        }

        $dependencies = array_map(function (ReflectionParameter $parameter) use ($arguments) {
            $variableName = $parameter->getName();

            if (array_key_exists($variableName, $arguments)) {
                return $arguments[$variableName];
            }

            /** @var ReflectionNamedType|null $type */
            $type = $parameter->getType();

            if (is_null($type)) {
                throw InvalidArgumentException::create("Cannot resolve parameter {$variableName} without a type");
            }

            if ($type->isBuiltin() && $parameter->isDefaultValueAvailable()) {
                return $parameter->getDefaultValue();
            }

            return $this->make($type->isBuiltin() ? $variableName : $type->getName(), $arguments);
        }, $constructor->getParameters());

        return $reflection->newInstanceArgs($dependencies);
    }
}
