<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\RouteCollectorInterface;
use Slim\Interfaces\RouteParserInterface;

/**
 * Class HomeController.
 */
class HomeController
{
    /**
     * @var RouteParserInterface
     */
    private $routeParser;

    /**
     * HomeController constructor.
     *
     * @param RouteCollectorInterface $routeParser
     */
    public function __construct(RouteCollectorInterface $routeParser)
    {
        $this->routeParser = $routeParser->getRouteParser();
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $uri = $this->routeParser->fullUrlFor($request->getUri(), 'hello', ['name' => pathinfo(str_replace('\\', '/', __CLASS__), PATHINFO_BASENAME)]);

        return $response
            ->withStatus(301)
            ->withHeader('Location', $uri);
    }
}
