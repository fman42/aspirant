<?php declare(strict_types=1);

namespace App\Support;

class CommandMap
{
    private array $map = [];

    public function set(string $name, string $value): void
    {
        $this->map[$name] = $value;
    }

    public function getMap(): array
    {
        return $this->map;
    }
}
