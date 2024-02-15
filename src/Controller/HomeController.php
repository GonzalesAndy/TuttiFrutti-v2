<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\DiscogsApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function index(): Response
    {
        return $this->render('pages/home.html.twig', [
            'title' => 'Home',
        ]);
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

    #[Route('/searchAbout{title}', name: 'search')]
    public function search(string $title): Response
    {
        $type = 'master';
        
        $result = $this->discogsApiService->search($title, $type);

        return $this->render('pages/results.html.twig', [
            'title' => $title,
            'results' => $result['results'],
        ]);
    }
}
