<?php

namespace App\Controller;

use App\Entity\Fruit;
use Doctrine\ORM\EntityManagerInterface;
use App\Service\DiscogsApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{


    private DiscogsApiService $discogsApiService;

    public function __construct(DiscogsApiService $discogsApiService)
    {
        $this->discogsApiService = $discogsApiService;
    }


    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $fruits = $entityManager->getRepository(Fruit::class)->findAll();
        return $this->render('pages/home.html.twig', [
            'title' => 'Home',
            'fruits' => $fruits,
        ]);
    }

    #[Route('/autocompleteList', name: 'autocompleteList', methods: ['GET'])]
    public function autocompleteList(EntityManagerInterface $entityManager): JsonResponse
    {
        // Renvoie tous les fruits dispinibles dans la base de données
        $fruits = $entityManager->getRepository(className: Fruit::class)->findAll();

        $fruitNames = array_map(function($fruit) {
            return $fruit->getName();
        }, $fruits);

        return $this->json($fruitNames);
    }

    #[Route('/search/{title}/{pagination}', name: 'search')]
    public function search(string $title, int $pagination): Response
    {
        // Recherche d'albums en utilisation l'api discogs
        $type = 'master';
        $result = $this->discogsApiService->multipleLanguageSearch($title, $type, $pagination);

        return $this->render('pages/search.html.twig', [
            'title' => $title,
            'results' => $result,
            'pagination' => $pagination
        ]);
    }
}
