<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Class CommandMap.
 */
class CommandMap
{
    /**
     * @var array
     */
    private $map = [];

    /**
     * @param string $name
     * @param string $value
     */
    public function set(string $name, string $value): void
    {
        $this->map[$name] = $value;
    }

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }
}
