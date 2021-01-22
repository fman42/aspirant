<?php declare(strict_types=1);

namespace App\Container;

use Psr\Container\NotFoundExceptionInterface;

class ServiceNotFoundException extends \InvalidArgumentException implements NotFoundExceptionInterface
{
    public static function createFromServiceId(string $id): self
    {
        $message = \sprintf('Service %s requested from container, but not found', $id);

        return new static($message);
    }
}
