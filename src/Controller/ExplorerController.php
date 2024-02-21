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
        $result['liked'] = false;
        $user = $this->getUser();
        if ($user) {
            $album = $entityManager->getRepository(Album::class)->find($result['id']);
            if ($album) {
                if ($user->getFavorite()->contains($album)) {
                    $result['liked'] = true;
                }
            }
        }
        dump($result);
        return $this->render('pages/show.html.twig', [
            'title' => 'Explorer',
            'result' => $result,
        ]);
    }

    private function updateRelationships(EntityManagerInterface $entityManager, Album $album, string $field, string $class, string $property)
    {
        if (isset($data['result'][$field])) {
            foreach ($data['result'][$field] as $item) {
                $entity = $entityManager->getRepository($class)->findOneBy([$property => $item]);
                if (!$entity) {
                    $entity = new $class();
                    $entity->{"set$property"}($item);
                    $entityManager->persist($entity);
                }
                $album->{"add$field"}($entity);
            }
        }
    }

    #[Route('/add-favorite', name: 'add_favorite', methods: ['POST'])]
    public function addFavorite(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $albumId = $data['id'];

        $album = $entityManager->getRepository(Album::class)->find($albumId) ?? (new Album())
            ->setId($albumId)
            ->setTitle($data['title'])
            ->setArtist($data['artists'][0]['name'])
            ->setYear($data['year'])
            ->setCountry(isset($data['country']) ? $data['country'] : 'Unknown')
            ->setCoverImage($data['images'][0]['uri'])
            ->setDiscogLink($data['uri'])
            ->setLikes(1);

        $this->updateRelationships($entityManager, $album, 'styles', Style::class, 'Style');
        $this->updateRelationships($entityManager, $album, 'genres', Genre::class, 'Genre');

        $album->setLikes($album->getLikes() + 1);

        $user = $this->getUser();
        $user->addFavorite($album);

        $entityManager->persist($album);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse('success', 200);
    }


    #[Route('/remove-favorite', name: 'remove_favorite', methods: ['POST'])]
    public function removeFavorite(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $album = $entityManager->getRepository(Album::class)->find($data['result']['id']);
        $user = $this->getUser();
        $user->removeFavorite($album);
        $entityManager->persist($user);
        $album->setLikes($album->getLikes() - 1);
        $entityManager->persist($album);
        $entityManager->flush();
        return new JsonResponse('success', 200);
    }
}