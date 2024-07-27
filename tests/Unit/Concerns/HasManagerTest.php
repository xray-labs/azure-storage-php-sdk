<?php

declare(strict_types=1);

use Xray\AzureStoragePhpSdk\Concerns\HasManager;
use Xray\AzureStoragePhpSdk\Contracts\Manager;
use Xray\AzureStoragePhpSdk\Exceptions\ManagerNotSetException;

uses()->group('concerns', 'traits');

it('should throw an exception if the manager is not set', function () {
    $class = new class () {
        /** @use HasManager<Manager> */
        use HasManager;

        public function throwException(): void
        {
            $this->ensureManagerIsConfigured();
        }
    };

    $class->throwException();
})->throws(ManagerNotSetException::class);

it('should get the manager out of the trait', function () {
    $manager = new class () implements Manager {
        //
    };

    $class = new class ($manager) {
        /** @use HasManager<Manager> */
        use HasManager;

        public function __construct(Manager $manager)
        {
            $this->manager = $manager;
        }
    };

    expect($class->getManager())
        ->toBeInstanceOf(Manager::class)
        ->toBe($manager);
});

it('should set the manager in the trait', function () {
    $manager = new class () implements Manager {
        //
    };

    $class = new class ($manager) {
        /** @use HasManager<Manager> */
        use HasManager;

        public function __construct(protected Manager $managerToCheck)
        {
            //
        }

        public function assertManagerWasSet(): void
        {
            expect($this->manager)
                ->toBeInstanceOf(Manager::class)
                ->toBe($this->managerToCheck);
        }
    };

    $class->setManager($manager)
        ->assertManagerWasSet();
});
