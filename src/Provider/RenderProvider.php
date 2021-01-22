<?php declare(strict_types=1);

namespace App\Provider;

use App\Container\Container;
use App\Support\Config;
use App\Support\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class RenderProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->set(Environment::class, static function (ContainerInterface $container) {
            $config = $container->get(Config::class);
            $loader = new FilesystemLoader($config->get('templates')['dir']);
            $cache = $config->get('templates')['cache'];

            $options = [
                'cache' => empty($cache) || $container->get(Config::class)->get('environment') === 'dev' ? false : $cache,
            ];

            return new Environment($loader, $options);
        });
    }
}
