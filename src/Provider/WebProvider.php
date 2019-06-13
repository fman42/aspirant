<?php
/**
 * 2019-06-13.
 */

declare(strict_types=1);

namespace App\Provider;

use App\Controller\HelloController;
use App\Controller\HomeController;
use App\Support\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;
use UltraLite\Container\Container;

/**
 * Class WebProvider.
 */
class WebProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        $container->set(HomeController::class, static function (ContainerInterface $container) {
            return new HomeController($container->get(RouteCollectorInterface::class));
        });

        $container->set(HelloController::class, static function (ContainerInterface $container) {
            return new HelloController($container->get(Environment::class));
        });

        $router = $container->get(RouteCollectorInterface::class);

        $router->group('/', function (RouteCollectorProxyInterface $router) {
            $routes = self::getRoutes();
            foreach ($routes as $routeName => $routeConfig) {
                $router->{$routeConfig['method']}($routeConfig['path'] ?? '', $routeConfig['controller'] . ':' . $routeConfig['action'])
                    ->setName($routeName);
            }
        });
    }

    /**
     * @return array
     */
    protected static function getRoutes(): array
    {
        return Yaml::parseFile(dirname(__DIR__, 2) . '/config/routes.yaml');
    }
}
