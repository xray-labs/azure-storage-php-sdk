<?php

declare(strict_types = 1);

namespace Sjpereira\AzureStoragePhpSdk\BlobStorage\Entities\Container;

class Containers
{
    protected array $containers = [];

    public function __construct(array $containers = [])
    {
        print_r($containers);

        foreach ($containers as $container) {
            print_r($container);

            die;
            $this->containers[] = new Container($container);
        }
    }
}
