<?php
namespace App\Controller;

use App\Entity\Album;
use App\Entity\Fruit;
use App\Entity\Genre;
use App\Entity\Style;
use App\Entity\Tracklist;
use App\Service\DiscogsApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ExplorerController extends AbstractController
{
    /*
     * Permet d'afficher les résultats de recherche pour 3 fruits pris aléatoirement
     */
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

    /*
     * Permet d'afficher les détails d'une musique
     */
    #[Route('/result/{id}', name: 'explorer_show')]
    public function show(DiscogsApiService $discogsApiService, EntityManagerInterface $entityManager, $id): Response
    {
        $album = $entityManager->getRepository(Album::class)->find($id);
        if ($album) {
            $result = [
                'id' => $album->getId(),
                'title' => $album->getTitle(),
                'artists' => [['name' => $album->getArtist()]],
                'year' => $album->getYear(),
                'country' => $album->getCountry(),
                'images' => [['uri' => $album->getCoverImage()]],
                'uri' => $album->getDiscogLink(),
                'tracklist' => $album->getTrack()->map(fn(Tracklist $track) => [
                    'title' => $track->getTitle(),
                    'duration' => $track->getDuration(),
                'genre' => $album->getGenres()->map(fn(Genre $genre) => $genre->getGenre())->toArray(),
                'style' => $album->getStyles()->map(fn(Style $style) => $style->getStyle())->toArray(),
                ])->toArray()
            ];
            if ($this->getUser()) {
                $result['liked'] = $this->getUser()->getFavorite()->contains($album);
            }
        } else {
            $result = $discogsApiService->getRelease($id);
            $result['liked'] = false;
        }
        return $this->render('pages/show.html.twig', [
            'title' => 'Explorer',
            'result' => $result
        ]);
    }

    /*
     * Permet d'ajouter les genres et styles d'un album
     */
    private function updateRelationships(EntityManagerInterface $entityManager, Album $album, string $field, string $class, string $property): void
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

    /*
     * Permet d'ajouter un album aux favoris de l'utilisateur connecté
     */
    #[Route('/add-favorite', name: 'add_favorite', methods: ['POST'])]
    public function addFavorite(EntityManagerInterface $entityManager, Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $albumId = $data['id'];

        $album = $entityManager->getRepository(Album::class)->find($albumId) ?? (new Album())
            ->setId($albumId)
            ->setTitle($data['title'])
            ->setArtist($data['artists'][0]['name'])
            ->setYear($data['year'] ?? 0)
            ->setCountry($data['country'] ?? 'Unknown')
            ->setCoverImage($data['images'][0]['uri'] ?? 'https://m.media-amazon.com/images/I/61QbG3IAqlL._AC_UF350,350_QL80_.jpg')
            ->setDiscogLink($data['uri'])
            ->setLikes(1);

        $this->updateRelationships($entityManager, $album, 'styles', Style::class, 'Style');
        $this->updateRelationships($entityManager, $album, 'genres', Genre::class, 'Genre');
        // add tracklist
        if (isset($data['tracklist'])) {
            foreach ($data['tracklist'] as $track) {
                $tracklist = new Tracklist();
                $tracklist->setTitle($track['title']);
                if (isset($track['duration'])) {
                    $tracklist->setDuration($track['duration']);
                }
                $album->addTrack($tracklist);
                $entityManager->persist($tracklist);
            }
        }

        $album->setLikes($album->getLikes() + 1);

        $user = $this->getUser();
        $user->addFavorite($album);

        $entityManager->persist($album);
        $entityManager->persist($user);
        $entityManager->flush();

        return new JsonResponse('success', 200);
    }


    /*
     * Permet de retirer un album des favoris de l'utilisateur connecté
     */
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

    #[Route('/favorite', name: 'favorite')]
    public function favorite(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $favorite = $this->getUser()->getFavorite();
        return $this->render('pages/favorite.html.twig', [
            'title' => 'Favorite',
            'favorite' => $favorite
        ]);
    }
}