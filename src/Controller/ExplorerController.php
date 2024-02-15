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
            'grenade' => 'La grenade, la touche explosive en musique.',
            'pomme' => 'La pomme, le fruit qui donne du croquant aux mélodies.',
            'kiwi' => 'Le kiwi, de la pulpe verte aux airs enjoués.',
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