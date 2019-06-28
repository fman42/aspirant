<?php

declare(strict_types=1);

namespace App\Command;

use Slim\Interfaces\RouteCollectorInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class RouteListCommand.
 */
class RouteListCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'debug:router';

    /**
     * @var RouteCollectorInterface
     */
    private $router;

    /**
     * RouteListCommand constructor.
     *
     * @param RouteCollectorInterface $router
     * @param string|null             $name
     */
    public function __construct(RouteCollectorInterface $router, string $name = null)
    {
        parent::__construct($name);
        $this->router = $router;
    }

    protected function configure(): void
    {
        $this->setDescription('List of application routes');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Application routes');

        $items = [];
        $routes = $this->router->getRoutes();
        if (empty($routes)) {
            $io->writeln('Routes list is empty');

            return 0;
        }

        foreach ($routes as $route) {
            $items[] = [
                'path' => $route->getPattern(),
                'methods' => implode(', ', $route->getMethods()),
                'name' => $route->getName(),
                'handler' => $route->getCallable(),
            ];
        }

        $io->table(['Path', 'Methods', 'Name', 'Handler'], $items);

        return 0;
    }
}
