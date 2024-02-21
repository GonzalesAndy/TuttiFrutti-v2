<?php
namespace App\Controller;

use App\Entity\Album;
use App\Entity\Fruit;
use App\Entity\Genre;
use App\Entity\Style;
use App\Service\DiscogsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/result/{id}', name: 'explorer_show')]
    public function show(DiscogsApiService $discogsApiService, EntityManagerInterface $entityManager, $id): Response
    {
        $result = $discogsApiService->getRelease($id);

        // dump($result);

        return $this->render('pages/show.html.twig', [
            'title' => 'Explorer',
            'result' => $result,
        ]);
    }

    #[Route('/add-favorite', name: 'add_favorite', methods: ['POST'])]
    public function addFavorite(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $album = $entityManager->getRepository(Album::class)->find($data['result']['id']);
        if (!$album) {
            $album = new Album();
            $album->setId($data['result']['id']);
            $album->setTitle($data['result']['title']);
            $album->setArtist($data['result']['artists'][0]['name']);
            $album->setYear($data['result']['year']);
            if(isset($data['result']['country'])) {
                $album->setCountry($data['result']['country']);
            } else {
                $album->setCountry('Unknown');
            }
            if (isset($data['result']['styles'])) {
                for ($i = 0; $i < count($data['result']['styles']); $i++) {
                    $style = $entityManager->getRepository(Style::class)->findOneBy(['style' => $data['result']['styles'][$i]]);
                    if (!$style) {
                        $style = new Style();
                        $style->setStyle($data['result']['styles'][$i]);
                        $entityManager->persist($style);
                    }
                    $album->addStyle($style);
                }
            }
            if (isset($data['result']['genres'])) {
                for ($i = 0; $i < count($data['result']['genres']); $i++) {
                    $genre = $entityManager->getRepository(Genre::class)->findOneBy(['genre' => $data['result']['genres'][$i]]);
                    if (!$genre) {
                        $genre = new Genre();
                        $genre->setGenre($data['result']['genres'][$i]);
                        $entityManager->persist($genre);
                    }
                    $album->addGenre($genre);
                }
            }
            $album->setCoverImage($data['result']['images'][0]['uri']);
            $album->setDiscogLink($data['result']['uri']);
            $album->setLikes(1);
            $entityManager->persist($album);
        } else {
            $album->setLikes($album->getLikes() + 1);
        }
        $user = $this->getUser();
        $user->addFavorite($album);
        $entityManager->persist($user);
        $entityManager->flush();
        return new JsonResponse($album->getLikes());
    }
}