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
            'banane' => 'La banane, un fruit exotique, des tubes ensoleillés.',
            'pomme' => 'La pomme, le fruit qui donne du croquant aux mélodies.',
            'fraise' => 'De la graine à la mélodie, les fraises en musique.',
            'framboise' => 'La framboise, un fruit qui donne une couleur unique aux rythmes.'
        ];
        $type = 'master';
        $results = [];

        foreach ($descriptons as $title => $description) {
            $result = $discogsApiService->multipleLanguageSearch($title, $type);
            $results[$title] = $result;
            shuffle($results[$title]);
        }


        return $this->render('pages/explorer.html.twig', [
            'title' => 'Explorer',
            'results' => $results,
            'descriptions' => $descriptons
        ]);
    }
}