<?php declare(strict_types=1);

namespace App\Provider;

use App\Container\Container;
use App\Controller\HomeController;
use App\Support\Config;
use App\Support\ServiceProviderInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Symfony\Component\Yaml\Yaml;
use Twig\Environment;

class WebProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $this->defineControllerDi($container);
        $this->defineRoutes($container);
    }

    protected function defineControllerDi(Container $container): void
    {
        $container->set(HomeController::class, static function (ContainerInterface $container) {
            return new HomeController($container->get(RouteCollectorInterface::class), $container->get(Environment::class), $container->get(EntityManagerInterface::class));
        });
    }

    protected function defineRoutes(Container $container): void
    {
        $router = $container->get(RouteCollectorInterface::class);

        $router->group('/', function (RouteCollectorProxyInterface $router) use ($container) {
            $routes = self::getRoutes($container);
            foreach ($routes as $routeName => $routeConfig) {
                $router->{$routeConfig['method']}($routeConfig['path'] ?? '', $routeConfig['controller'] . ':' . $routeConfig['action'])
                    ->setName($routeName);
            }
        });
    }

    protected static function getRoutes(Container $container): array
    {
        return Yaml::parseFile($container->get(Config::class)->get('base_dir') . '/config/routes.yaml');
    }
}
