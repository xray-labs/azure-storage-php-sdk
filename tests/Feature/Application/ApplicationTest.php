<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Application\Application;
use Xray\AzureStoragePhpSdk\Exceptions\InvalidArgumentException;

uses()->group('applications');

afterEach(fn () => Application::getInstance()->flush());

it('should get the container instance as singleton', function () {
    expect(Application::getInstance())
        ->toBeInstanceOf(Application::class)
        ->toBe(Application::getInstance());
});

it('should bind an instance to the container', function () {
    $container = Application::getInstance()
        ->instance('abstract', $instance = new class () {});

    expect($container->make('abstract'))
        ->toBeObject()
        ->toBe($instance);
});

it('should bind a singleton to the container', function () {
    $container = Application::getInstance()
        ->singleton('testing', fn () => (new class () {}));

    expect($instance = $container->make('testing'))
        ->toBeObject()
        ->toBe($container->make('testing'));

    $container->singleton('testing', fn () => (new class () {}));

    expect($instance)
        ->toBeObject()
        ->not->toBe($container->make('testing'));
});

it('should bind a class to the container', function (string $abstract, ?Closure $callback = null) {
    $container = Application::getInstance()->bind($abstract, $callback);

    expect($instance = $container->make($abstract))
        ->toHaveProperty('test', 'test_value');

    expect($container->make($abstract))
        ->not->toBe($instance);
})->with([
    'With callback' => ['abstract', fn () => (new class () {
        public string $test = 'test_value';
    })],
    'Without callback' => [TestWithNoConstructor::class],
]);

it('should throws if the provided class does not exists', function () {
    Application::getInstance()->make('testing');
})->throws(InvalidArgumentException::class, 'Cannot resolve class testing');

it('should scope a class to the container', function (string $abstract, ?Closure $callback = null) {
    $container = Application::getInstance()
        ->scope($abstract, $callback);

    expect($instance = $container->make($abstract))
        ->toHaveProperty('test', 'test_value');

    $container->scope($abstract, $callback);

    expect($instance)
        ->toHaveProperty('test', 'test_value')
        ->not->toBe($container->make($abstract));
})->with([
    'With callback' => ['abstract', fn () => (new class () {
        public string $test = 'test_value';
    })],
    'Without callback' => [TestWithNoConstructor::class],
]);

it('should not be able to scope a class that does not exists', function () {
    Application::getInstance()->scope('testing');
})->throws(InvalidArgumentException::class, 'Cannot scope testing without a callback');

it('should check if there\'s a class bound to the container', function (string $method = '', bool $withValue = false, bool $resolve = false) {
    $container = Application::getInstance();

    if ($bound = ($method && $withValue)) {
        $container->{$method}('key', $resolve ? new class () {} : fn () => new class () {});
    }

    expect($container->bound('key'))
        ->toBe($bound);
})->with([
    'Instance'      => ['instance', true, true],
    'Singleton'     => ['singleton', true],
    'Binding'       => ['bind', true],
    'Scope'         => ['scope', true],
    'Nothing Bound' => [],
]);

it('should make a class with no constructor', function () {
    expect(Application::getInstance()->make(TestWithNoConstructor::class))
        ->toBeInstanceOf(TestWithNoConstructor::class)
        ->toHaveProperty('test', 'test_value');
});

it('should make a class with constructor dependency', function () {
    $container = Application::getInstance()->bind('test', fn () => 'test_value');

    expect($instance = $container->make(TestWithConstructor::class, ['parameter' => 'test']))
        ->toBeInstanceOf(TestWithConstructor::class)
        ->and($instance->dependency)
        ->toBeInstanceOf(TestWithNoConstructor::class)
        ->toHaveProperty('test', 'test_value');
});

it('should make a class with no typed constructor when the parameter is defined', function () {
    expect(Application::getInstance()->make(TestConstructorWithoutTyping::class, ['test' => 'test_value']))
        ->toBeInstanceOf(TestConstructorWithoutTyping::class)
        ->toHaveProperty('test', 'test_value');
});

it('should make a class when it has a default value', function () {
    expect(Application::getInstance()->make(TestConstructorWithDefaultValue::class))
        ->toBeInstanceOf(TestConstructorWithDefaultValue::class)
        ->toHaveProperty('test', 'test_value');
});

it('should throw an exception when building a no typed class', function () {
    expect(Application::getInstance()->make(TestConstructorWithoutTyping::class))
        ->toBeInstanceOf(TestConstructorWithoutTyping::class);
})->throws(InvalidArgumentException::class, 'Cannot resolve parameter $test without one defined type');

it('should throw an exception when binding an abstract class without a callback', function () {
    Application::getInstance()->bind('abstract');
})->throws(InvalidArgumentException::class, 'Cannot bind abstract without a callback');

it('should resolve a callable a method with the container', function () {
    /** @var object{withConstructor: TestWithConstructor, test: string} $result */
    $result = Application::getInstance()->call(function (TestWithConstructor $withConstructor, string $test): object {
        return (object)[
            'withConstructor' => $withConstructor,
            'test'            => $test,
        ];
    }, ['test' => 'test_value']);

    expect($result)
        ->toBeObject()
        ->toHaveProperty('withConstructor')
        ->toHaveProperty('test', 'test_value')
        ->and($result->withConstructor)
        ->toBeInstanceOf(TestWithConstructor::class);
});

it('should flush all resolved instances and bindings from the container', function () {
    $container = Application::getInstance()
        ->instance('instance', new class () {})
        ->bind('bind', fn () => true)
        ->singleton('singleton', fn () => true)
        ->scope('scope', fn () => true);

    $container->flush();

    expect(false)
        ->toBe($container->bound('instance'))
        ->toBe($container->bound('bind'))
        ->toBe($container->bound('singleton'))
        ->toBe($container->bound('scope'));
});

it('should flush all scoped instances and bindings from the container', function () {
    $container = Application::getInstance()
        ->instance('instance', new class () {})
        ->bind('bind', fn () => true)
        ->singleton('singleton', fn () => true)
        ->scope('scope', fn () => true);

    $container->flushScoped();

    expect(true)
        ->toBe($container->bound('instance'))
        ->toBe($container->bound('bind'))
        ->toBe($container->bound('singleton'));

    expect(false)
        ->toBe($container->bound('scope'));
});

class TestWithNoConstructor
{
    public string $test = 'test_value';
}

class TestConstructorWithoutTyping
{
    public function __construct(public $test) // @phpstan-ignore-line
    {
        //
    }
}

class TestConstructorWithDefaultValue
{
    public function __construct(public string $test = 'test_value')
    {
        //
    }
}

class TestWithConstructor
{
    public function __construct(public TestWithNoConstructor $dependency)
    {
        //
    }
}
