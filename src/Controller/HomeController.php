<?php

namespace App\Controller;

use App\Service\DiscogsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $fruits = ['pomme', 'banane', 'orange', 'fraise'];

        return $this->render('pages/home.html.twig', [
            'title' => 'Home',
            'fruits' => $fruits,
        ]);
    }

    #[Route('/test', name: 'test')]
    public function test(DiscogsApiService $discogsApiService): Response
    {
        $title = 'banana';
        $type = 'master';

        $result = $discogsApiService->search($title, $type);

        return $this->render('pages/test.html.twig', [
            'title' => 'Test',
            'results' => $result['results'],
        ]);
    }
}
