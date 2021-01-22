<?php declare(strict_types=1);

namespace App\Support;

use Symfony\Component\Yaml\Yaml;

class Config
{
    private array $config = [];

    public function __construct(string $dir, string $env, string $root)
    {
        if (!\is_dir($dir)) {
            throw new \RuntimeException(sprintf('Config directory %s not found', $dir));
        }

        $config = Yaml::parseFile($dir . '/app.yaml');
        $envConfigPath = $dir . '/app.' . $env . '.yaml';
        if (\is_readable($envConfigPath)) {
            $config = \array_replace_recursive($config, Yaml::parseFile($envConfigPath));
        }

        foreach ($config as $item => $value) {
            $this->config[$item] = $value;
        }

        if (\is_readable($dir . '/monolog.yaml')) {
            $this->config = array_merge($this->config, Yaml::parseFile($dir . '/monolog.yaml')['monolog']);
        }

        if (\is_readable($dir . '/http-client.yaml')) {
            $this->config = array_merge($this->config, Yaml::parseFile($dir . '/http-client.yaml'));
        }

        if (\is_readable($dir . '/doctrine.yaml')) {
            $doctrineConfig = Yaml::parseFile($dir . '/doctrine.yaml');

            foreach ($doctrineConfig['mapping'] as $n => $mappingItem) {
                if (\is_dir($root . '/src/' . \ltrim($mappingItem, '/'))) {
                    $doctrineConfig['mapping'][$n] = $root . '/src/' . ltrim($mappingItem, '/');
                }
            }
            $this->config['doctrine'] = $doctrineConfig;
        }

        $this->config['environment'] = $env;
        $this->config['base_dir'] = $root;

        $this->resolveDirectories($root);
    }

    private function resolveDirectories(string $root): void
    {
        if (!isset($this->config['templates'])) {
            throw new \RuntimeException('\'templates\' parameter in config is required');
        }
        if (!\is_array($this->config['templates'])) {
            throw new \RuntimeException('\'templates\' parameter in config must be an array');
        }
        foreach ($this->config['templates'] as $name => $value) {
            if (empty($value)) {
                $this->config['templates'][$name] = null;
                continue;
            }

            if (\str_starts_with($value, '/')) {
                continue;
            }

            $this->config['templates'][$name] = $root . '/' . $value;
        }
    }

    public function get(string $name)
    {
        return $this->config[$name] ?? null;
    }
}
