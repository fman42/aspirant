<?php namespace App\Support;

use App\Container\Container;

interface ServiceProviderInterface
{
    public function register(Container $container): void;
}
