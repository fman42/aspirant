<?php declare(strict_types=1);

namespace App\Provider;

use App\Container\Container;
use App\Support\{CommandMap, Config, LoggerErrorHandler, NotFoundHandler, ServiceProviderInterface};
use GuzzleHttp\Client as GuzzleClient;
use Http\Adapter\Guzzle7\Client as GuzzleAdapter;
use Monolog\{Formatter\FormatterInterface, Handler\HandlerInterface, Logger};
use Psr\{Container\ContainerInterface,
    Http\Client\ClientInterface,
    Http\Message\ResponseFactoryInterface,
    Log\LoggerInterface};
use Slim\{CallableResolver,
    Exception\HttpNotFoundException,
    Interfaces\CallableResolverInterface,
    Interfaces\RouteCollectorInterface,
    Interfaces\RouteResolverInterface,
    Middleware\ErrorMiddleware,
    Middleware\RoutingMiddleware,
    Psr7\Factory\ResponseFactory,
    Routing\RouteCollector,
    Routing\RouteResolver};
use Twig\Environment;

class AppProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        // Console commands
        $container->set(CommandMap::class, static function () {
            return new CommandMap();
        });

        // Response
        $container->set(ResponseFactory::class, static function () {
            return new ResponseFactory();
        });

        // … and response factory
        $container->set(ResponseFactoryInterface::class, static function (ContainerInterface $container) {
            return $container->get(ResponseFactory::class);
        });

        // Callable resolver by Slim
        $container->set(CallableResolver::class, static function (ContainerInterface $container) {
            return new CallableResolver($container);
        });

        // Define callable resolver implementation for interface
        $container->set(CallableResolverInterface::class, static function (ContainerInterface $container) {
            return $container->get(CallableResolver::class);
        });

        // Route collector
        $container->set(RouteCollector::class, static function (ContainerInterface $container) {
            return new RouteCollector($container->get(ResponseFactoryInterface::class), $container->get(CallableResolverInterface::class), $container);
        });

        // … and route collector interface
        $container->set(RouteCollectorInterface::class, static function (ContainerInterface $container) {
            return $container->get(RouteCollector::class);
        });

        // Route resolver implementation
        $container->set(RouteResolver::class, static function (ContainerInterface $container) {
            return new RouteResolver($container->get(RouteCollectorInterface::class));
        });

        // Route resolver interface
        $container->set(RouteResolverInterface::class, static function (ContainerInterface $container) {
            return $container->get(RouteResolver::class);
        });

        // Monolog
        $container->set(Logger::class, static function (ContainerInterface $container) {
            $config = (array) $container->get(Config::class)->get('monolog');
            $logger = new Logger('default');
            foreach ($config as $loggerConfig) {
                $handler = new $loggerConfig['class'](...$loggerConfig['arguments']);
                if (!($handler instanceof HandlerInterface)) {
                    throw new \RuntimeException(sprintf('Class %s not implements %s', get_class($handler), HandlerInterface::class));
                }
                if (array_key_exists('formatter', $loggerConfig)) {
                    $formatter = new $loggerConfig['formatter']['class'](...$loggerConfig['formatter']['arguments']);
                    if ($formatter instanceof FormatterInterface) {
                        $handler->setFormatter($formatter);
                    }
                }
                $logger->pushHandler($handler);
            }

            return $logger;
        });

        // Interface for logger
        $container->set(LoggerInterface::class, static function (ContainerInterface $container) {
            return $container->get(Logger::class);
        });

        // Errors
        $container->set(LoggerErrorHandler::class, static function (ContainerInterface $container) {
            return new LoggerErrorHandler(
                $container->get(CallableResolverInterface::class),
                $container->get(ResponseFactoryInterface::class),
                $container->get(LoggerInterface::class)
            );
        });

        // Errors
        $container->set(NotFoundHandler::class, static function (ContainerInterface $container) {
            return new NotFoundHandler($container->get(ResponseFactoryInterface::class), $container->get(Environment::class));
        });

        // Errors
        $container->set(ErrorMiddleware::class, static function (ContainerInterface $container) {
            $middleware = new ErrorMiddleware(
                $container->get(CallableResolverInterface::class),
                $container->get(ResponseFactoryInterface::class),
                $container->get(Config::class)->get('slim')['debug'],
                true,
                true
            );

            $middleware->setErrorHandler(HttpNotFoundException::class, $container->get(NotFoundHandler::class));
            $middleware->setDefaultErrorHandler($container->get(LoggerErrorHandler::class));

            return $middleware;
        });

        // Middleware for routing
        $container->set(RoutingMiddleware::class, static function (ContainerInterface $container) {
            return new RoutingMiddleware($container->get(RouteResolverInterface::class), $container->get(RouteCollector::class)->getRouteParser());
        });

        $container->set(GuzzleAdapter::class, static function (ContainerInterface $container) {
            $config = $container->get(Config::class)->get('httpClient');
            $guzzle = new GuzzleClient($config ?? []);

            return new GuzzleAdapter($guzzle);
        });

        $container->set(ClientInterface::class, static function (ContainerInterface $container) {
            return $container->get(GuzzleAdapter::class);
        });
    }
}
