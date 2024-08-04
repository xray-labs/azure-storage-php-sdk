<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\ApplicationContainer;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

uses()->group('applications');

afterEach(fn () => ApplicationContainer::getContainer()->flush());

it('should get the container instance as singleton', function () {
    $container = ApplicationContainer::getContainer();

    expect($container)
        ->toBeInstanceOf(ApplicationContainer::class);

    $container2 = ApplicationContainer::getContainer();

    expect($container)
        ->toBe($container2);
});

it('should instance a class', function () {
    $container = ApplicationContainer::getContainer();

    $container->instance('abstract', $instance = new class () {});

    expect($container->make('abstract'))
        ->toBe($instance);
});

it('should bind a class', function (string $abstract, ?Closure $callback = null) {
    $container = ApplicationContainer::getContainer();

    /** @phpstan-ignore-next-line */
    $container->bind($abstract, $callback);

    expect($instance = $container->make($abstract))
        ->toHaveProperty('test', 'test_value');

    expect($container->make($abstract))
        ->not->toBe($instance);
})->with([
    'With callback' => [
        'abstract', fn () => (new class () {
            public string $test = 'test_value';
        }),
    ],
    'Without callback' => [
        TestWithNoConstructor::class,
    ],
]);

it('should make a class with no constructor', function () {
    $container = ApplicationContainer::getContainer();

    expect($container->make(TestWithNoConstructor::class))
        ->toBeInstanceOf(TestWithNoConstructor::class)
        ->toHaveProperty('test', 'test_value');
});

it('should throws if the provided class does not exists', function () {
    ApplicationContainer::getContainer()
        ->make('testing');
})->throws(InvalidArgumentException::class, 'Cannot resolve class testing');

it('should make a class with constructor dependency', function () {
    $container = ApplicationContainer::getContainer();

    /** @phpstan-ignore-next-line */
    $container->bind('test', fn () => 'test_value');

    expect($instance = $container->make(TestWithConstructor::class, ['parameter' => 'test']))
        ->toBeInstanceOf(TestWithConstructor::class)
        ->and($instance->dependency) // @phpstan-ignore-line
        ->toBeInstanceOf(TestConstructorDependency::class)
        ->toHaveProperty('test', 'test_value')
        ->toHaveProperty('parameter', 'test');
});

it('should throw an exception when building a no typed class', function () {
    $container = ApplicationContainer::getContainer();

    expect($container->make(TestConstructorWithoutTyping::class))
        ->toBeInstanceOf(TestConstructorWithoutTyping::class);
})->throws(InvalidArgumentException::class, 'Cannot resolve parameter test without a type');

it('should throw an exception when binding an abstract class without a callback', function () {
    $container = ApplicationContainer::getContainer();

    $container->bind('abstract'); // @phpstan-ignore-line
})->throws(InvalidArgumentException::class, 'Cannot bind abstract class abstract without a callback');

class TestWithNoConstructor
{
    public string $test = 'test_value';
}

class TestConstructorDependency
{
    public function __construct(public string $test, public string $parameter)
    {

    }
}

class TestConstructorWithoutTyping
{
    public function __construct(public $test) // @phpstan-ignore-line
    {

    }
}

class TestWithConstructor
{
    public function __construct(public TestConstructorDependency $dependency)
    {

    }
}
