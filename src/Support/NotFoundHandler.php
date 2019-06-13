<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Interfaces\ErrorHandlerInterface;
use Throwable;
use Twig\Environment;

class NotFoundHandler implements ErrorHandlerInterface
{
    /**
     * @var ResponseFactoryInterface
     */
    private $factory;

    /**
     * @var Environment
     */
    private $environment;

    public function __construct(ResponseFactoryInterface $factory, Environment $environment)
    {
        $this->factory = $factory;
        $this->environment = $environment;
    }

    /**
     * @param ServerRequestInterface $request
     * @param Throwable              $exception
     * @param bool                   $displayErrorDetails
     * @param bool                   $logErrors
     * @param bool                   $logErrorDetails
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, Throwable $exception, bool $displayErrorDetails, bool $logErrors, bool $logErrorDetails): ResponseInterface
    {
        $response = $this->factory->createResponse(404);
        try {
            $response->getBody()->write($this->environment->render('404.html.twig'));
        } catch (\Exception $e) {
            return $response;
        }

        return $response;
    }
}
