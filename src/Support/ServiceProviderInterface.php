<?php

namespace App\Support;

use UltraLite\Container\Container;

/**
 * Interface ServiceProviderInterface.
 */
interface ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return mixed
     */
    public function register(Container $container);
}
