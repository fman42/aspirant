<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Movie;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Exception\HttpBadRequestException;
use Slim\Interfaces\RouteCollectorInterface;
use Twig\Environment;

class HomeController
{
    private EntityManagerInterface $doctrine;

    public function __construct(
        private RouteCollectorInterface $routeCollector,
        private Environment $twig,
        private EntityManagerInterface $em
    ) {
        $this->doctrine = $em;
    }

    public function index(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $data = $this->twig->render('home/index.html.twig', [
                'trailers' => $this->fetchData(),
            ]);
        } catch (\Exception $e) {
            throw new HttpBadRequestException($request, $e->getMessage(), $e);
        }

        $response->getBody()->write($data);

        return $response;
    }

    protected function fetchData(): Collection
    {
        $data = $this->em->getRepository(Movie::class)
            ->findAll();

        return new ArrayCollection($data);
    }

    public function trailerCard(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $trailer_id = (int) $args['trailer_id'];
        $item = $this->doctrine->getRepository(Movie::class)->findOneBy(['id' => $trailer_id]);
        if ($item === null)
            return $response->withStatus(404);

        $template = $this->twig->render('home/trailer.html.twig', compact('item'));
        $response->getBody()->write($template);
        return $response;
    }
}
