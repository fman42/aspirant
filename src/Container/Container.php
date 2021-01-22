<?php declare(strict_types=1);

namespace App\Container;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $services = [];

    private ContainerInterface $delegateContainer;

    public function __construct(private array $serviceFactories = [])
    {
        foreach ($serviceFactories as $serviceId => $serviceFactory) {
            $this->set($serviceId, $serviceFactory);
        }
    }

    public function set(string $serviceId, \Closure $serviceFactory): void
    {
        $this->serviceFactories[$serviceId] = $serviceFactory;
        unset($this->services[$serviceId]);
    }

    public function configureFromFile(string $path): void
    {
        foreach (require $path as $serviceId => $serviceFactory) {
            $this->set($serviceId, $serviceFactory);
        }
    }

    public function get(string $id): object
    {
        if (!$this->has($id)) {
            throw ServiceNotFoundException::createFromServiceId($id);
        }

        if (($this->services[$id] ?? null) === null) {
            $this->services[$id] = $this->serviceFromFactory($id);
        }

        return $this->services[$id];
    }

    public function has(string $id): bool
    {
        return ($this->serviceFactories[$id] ?? null) !== null;
    }

    private function serviceFromFactory(string $id): object
    {
        $factory = $this->serviceFactories[$id];
        $container = $this->delegateContainer ?? $this;

        return $factory($container);
    }
}
