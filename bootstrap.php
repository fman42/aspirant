<?php

use App\Provider\AppProvider;
use App\Provider\ConsoleCommandProvider;
use App\Support\Config;
use App\Support\ServiceProviderInterface;
use Symfony\Component\Dotenv\Dotenv;
use UltraLite\Container\Container;

require_once __DIR__ . '/vendor/autoload.php';

(new Dotenv())->loadEnv(__DIR__ . '/.env');

$env = getenv('APP_ENV');
if (!$env) {
    $env = 'dev';
}

$config = new Config(__DIR__ . '/config', $env, __DIR__);

$providers = [
    AppProvider::class,
    ConsoleCommandProvider::class,
];

$container = new Container([
    Config::class => static function () use ($config) { return $config; },
]);

foreach ($providers as $providerClassName) {
    if (!class_exists($providerClassName)) {
        throw new RuntimeException(sprintf('Provider %s not found', $providerClassName));
    }
    $provider = new $providerClassName;
    if (!($provider instanceof ServiceProviderInterface)) {
        throw new RuntimeException(sprintf('%s class is not a Service Provider', $providerClassName));
    }
    $provider->register($container);
}

return $container;
