<?php
namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use App\Service\DiscogsApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Webmozart\Assert\Tests\StaticAnalysis\string;

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
        return $this->render('pages/home.html.twig', [
            'title' => 'Home'
        ]);
    }

    #[Route('/test', name: 'test')]
    public function test(DiscogsApiService $discogsApiService): Response
    {
        $title = 'banana';
        $type = 'release';

        $result = $discogsApiService->search($title, $type);
        return $this->render('pages/test.html.twig', [
            'title' => 'Test',
            'results' => $result['results']
        ]);
    }
}