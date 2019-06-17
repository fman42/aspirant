<?php

declare(strict_types=1);

namespace App\Provider;

use App\Command\RouteListCommand;
use App\Support\CommandMap;
use App\Support\ServiceProviderInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use UltraLite\Container\Container;

/**
 * Class ConsoleCommandProvider.
 */
class ConsoleCommandProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        $container->set(RouteListCommand::class, static function (ContainerInterface $container) {
            return new RouteListCommand($container->get(RouteCollectorInterface::class));
        });

        $container->get(CommandMap::class)->set(RouteListCommand::getDefaultName(), RouteListCommand::class);
    }
}
