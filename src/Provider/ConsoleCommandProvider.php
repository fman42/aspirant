<?php

declare(strict_types=1);

namespace App\Provider;

use App\Command\FetchDataCommand;
use App\Command\RouteListCommand;
use App\Support\CommandMap;
use App\Support\ServiceProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;
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

        $container->set(FetchDataCommand::class, static function (ContainerInterface $container) {
            return new FetchDataCommand($container->get(ClientInterface::class), $container->get(LoggerInterface::class), $container->get(EntityManagerInterface::class));
        });

        $container->get(CommandMap::class)->set(RouteListCommand::getDefaultName(), RouteListCommand::class);
        $container->get(CommandMap::class)->set(FetchDataCommand::getDefaultName(), FetchDataCommand::class);
    }
}
