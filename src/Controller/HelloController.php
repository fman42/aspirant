<?php

declare(strict_types=1);

namespace App\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment;

/**
 * Class HelloController.
 */
class HelloController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * HelloController constructor.
     *
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('hello.html.twig', [
                'name' => $request->getAttribute('name'),
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Unable to render data: %s', $e->getMessage()), $e->getCode(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }
}
