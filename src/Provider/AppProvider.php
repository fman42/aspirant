<?php

declare(strict_types=1);

namespace App\Provider;

use App\Support\CommandMap;
use App\Support\Config;
use App\Support\LoggerErrorHandler;
use App\Support\NotFoundHandler;
use App\Support\ServiceProviderInterface;
use Monolog\Handler\HandlerInterface;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Log\LoggerInterface;
use Slim\CallableResolver;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\CallableResolverInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteResolverInterface;
use Slim\Middleware\ErrorMiddleware;
use Slim\Middleware\RoutingMiddleware;
use Slim\Psr7\Factory\ResponseFactory;
use Slim\Routing\RouteCollector;
use Slim\Routing\RouteResolver;
use Twig\Environment;
use UltraLite\Container\Container;

/**
 * Class AppProvider.
 */
class AppProvider implements ServiceProviderInterface
{
    /**
     * @param Container $container
     *
     * @return mixed|void
     */
    public function register(Container $container)
    {
        $container->set(CommandMap::class, static function () {
            return new CommandMap();
        });

        $container->set(ResponseFactory::class, static function () {
            return new ResponseFactory();
        });

        $container->set(ResponseFactoryInterface::class, static function (ContainerInterface $container) {
            return $container->get(ResponseFactory::class);
        });

        $container->set(CallableResolver::class, static function (ContainerInterface $container) {
            return new CallableResolver($container);
        });

        $container->set(CallableResolverInterface::class, static function (ContainerInterface $container) {
            return $container->get(CallableResolver::class);
        });

        $container->set(RouteCollector::class, static function (ContainerInterface $container) {
            return new RouteCollector($container->get(ResponseFactoryInterface::class), $container->get(CallableResolverInterface::class), $container);
        });

        $container->set(RouteCollectorInterface::class, static function (ContainerInterface $container) {
            return $container->get(RouteCollector::class);
        });

        $container->set(RouteResolver::class, static function (ContainerInterface $container) {
            return new RouteResolver($container->get(RouteCollectorInterface::class));
        });

        $container->set(RouteResolverInterface::class, static function (ContainerInterface $container) {
            return $container->get(RouteResolver::class);
        });

        $container->set(Logger::class, static function (ContainerInterface $container) {
            $config = $container->get(Config::class)->get('monolog');
            $logger = new Logger('default');
            foreach ($config as $loggerConfig) {
                $handler = new $loggerConfig['class'](...$loggerConfig['arguments']);
                if (!($handler instanceof HandlerInterface)) {
                    throw new \RuntimeException(sprintf('Class %s not implements %s', get_class($handler), HandlerInterface::class));
                }
                if (array_key_exists('formatter', $loggerConfig)) {
                    $formatter = new $loggerConfig['formatter']['class'](...$loggerConfig['formatter']['arguments']);
                    $handler->setFormatter($formatter);
                }
                $logger->pushHandler($handler);
            }

            return $logger;
        });

        $container->set(LoggerInterface::class, static function (ContainerInterface $container) {
            return $container->get(Logger::class);
        });

        $container->set(LoggerErrorHandler::class, static function (ContainerInterface $container) {
            return new LoggerErrorHandler($container->get(ResponseFactoryInterface::class), $container->get(LoggerInterface::class));
        });

        $container->set(NotFoundHandler::class, static function (ContainerInterface $container) {
            return new NotFoundHandler($container->get(ResponseFactoryInterface::class), $container->get(Environment::class));
        });

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

        $container->set(RoutingMiddleware::class, static function (ContainerInterface $container) {
            return new RoutingMiddleware($container->get(RouteResolverInterface::class));
        });
    }
}
