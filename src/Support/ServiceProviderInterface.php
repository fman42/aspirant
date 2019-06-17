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
     */
    public function register(Container $container);
}
