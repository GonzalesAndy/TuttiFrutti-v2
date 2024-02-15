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

        $descriptons = [
            'banane' => 'Ceci est une banane',
            'grenade' => 'Ceci est une grenade',
            'pomme' => 'Ceci est une pomme'
        ];
        $type = 'master';
        $results = [];

        foreach ($descriptons as $title => $description) {
            $result = $discogsApiService->search($title, $type);
            $results[$title] = $result['results'];
        }

        return $this->render('pages/explorer.html.twig', [
            'title' => 'Explorer',
            'results' => $results,
            'descriptions' => $descriptons
        ]);
    }
}