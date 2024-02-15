<?php
namespace App\Controller;

use App\Entity\Fruit;
use App\Service\DiscogsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExplorerController extends AbstractController
{
    #[Route('/explorer', name: 'explorer')]
    public function explorer(DiscogsApiService $discogsApiService, EntityManagerInterface $entityManager): Response
    {
        $results = [];
        $descriptions = [];
        $fruits = $entityManager->getRepository(className: Fruit::class)->findAll();
        shuffle($fruits);
        $fruits = array_slice($fruits, 0, 3);


        foreach ($fruits as $fruit) {
            $result = $discogsApiService->multipleLanguageSearch($fruit->getName(), 'master');
            $results[$fruit->getName()] = $result;
            $descriptions[$fruit->getName()] = $fruit->getDescription();
            shuffle($results[$fruit->getName()]);
        }

        return $this->render('pages/explorer.html.twig', [
            'title' => 'Explorer',
            'results' => $results,
            'descriptions' => $descriptions
        ]);
    }
}