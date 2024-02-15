<?php
namespace App\Controller;

use App\Service\DiscogsApiService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExplorerController extends AbstractController
{
    #[Route('/explorer', name: 'explorer')]
    public function explorer(DiscogsApiService $discogsApiService): Response
    {
        $titles = ['banane', 'grenade', 'pomme'];
        $type = 'master';
        $results = [];

        foreach ($titles as $title) {
            $result = $discogsApiService->search($title, $type);
            $results[$title] = $result['results'];
        }

        dump($results);

        return $this->render('pages/explorer.html.twig', [
            'title' => 'Explorer',
            'results' => $results
        ]);
    }
}