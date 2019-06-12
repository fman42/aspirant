<?php
/**
 * 2019-06-12 21:56.
 */

declare(strict_types=1);

namespace App\Support;

use Symfony\Component\Yaml\Yaml;

/**
 * Class Config.
 */
class Config
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * Config constructor.
     *
     * @param string $dir
     * @param string $env
     * @param string $root
     */
    public function __construct(string $dir, string $env, string $root)
    {
        if (!is_dir($dir)) {
            return;
        }

        $config = Yaml::parseFile($dir . '/app.yaml');
        $envConfigPath = $dir . '/' . $env . '.app.yaml';
        if (is_readable($envConfigPath)) {
            $config = array_replace_recursive($config, Yaml::parseFile($envConfigPath));
        }

        foreach ($config as $item => $value) {
            $this->config[$item] = $value;
        }

        $this->resolveDirectories($root);
    }

    /**
     * @param string $root
     */
    private function resolveDirectories(string $root): void
    {
        if (!isset($this->config['templates'])) {
            throw new \RuntimeException('\'templates\' parameter in config is required');
        }
        if (!is_array($this->config['templates'])) {
            throw new \RuntimeException('\'templates\' parameter in config must be an array');
        }
        foreach ($this->config['templates'] as $name => $value) {
            if (strpos($value, '/') === 0) {
                continue;
            }

            if (empty($value)) {
                $this->config['templates'][$name] = null;
                continue;
            }

            $this->config['templates'][$name] = $root . '/' . $value;
        }
    }

    /**
     * @param string $name
     *
     * @return mixed|null
     */
    public function get(string $name)
    {
        return array_key_exists($name, $this->config) ? $this->config[$name] : null;
    }
}
