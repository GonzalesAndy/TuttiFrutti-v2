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

    private $entityManager;

    private DiscogsApiService $discogsApiService;

    public function __construct(EntityManagerInterface $entityManager, DiscogsApiService $discogsApiService)
    {
        $this->entityManager = $entityManager;
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
        $fruits = $entityManager->getRepository(className: Fruit::class)->findAll();

        $fruitNames = array_map(function($fruit) {
            return $fruit->getName();
        }, $fruits);

        return $this->json($fruitNames);
    }

    #[Route('/test', name: 'test')]
    public function test(): Response
    {
        $title = 'banana';
        $type = 'master';

        $result = $this->discogsApiService->search($title, $type);

        return $this->render('pages/test.html.twig', [
            'title' => 'Test',
            'results' => $result['results'],
        ]);
    }

    #[Route('/search{title}/{pagination}', name: 'search')]
    public function search(string $title, int $pagination): Response
    {
        $type = 'master';
        
        $result = $this->discogsApiService->search($title, $type);

        return $this->render('pages/results.html.twig', [
            'title' => 'Tutti Fruiti - ' . $title,
            'results' => $result['results'],
        ]);
    }
}
