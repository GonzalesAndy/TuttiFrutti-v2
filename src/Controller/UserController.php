<?php
namespace App\Controller;

use App\Entity\Album;
use App\Entity\Genre;
use App\Entity\Style;
use App\Entity\Tracklist;
use App\Repository\AlbumRepository;
use App\Repository\TracklistRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /*
     * Permet d'ajouter les genres et styles d'un album
     */
    private function updateRelationships(EntityManagerInterface $entityManager, Album $album, string $field, string $class, string $property): void
    {
        // Récupération des genres et styles de l'album
        if (isset($data['result'][$field])) {
            foreach ($data['result'][$field] as $item) {
                // Si le genre ou le style n'existe pas, on le crée
                $entity = $entityManager->getRepository($class)->findOneBy([$property => $item]);
                if (!$entity) {
                    // Création d'une nouvelle entité
                    $entity = new $class();
                    $entity->{"set$property"}($item);
                    $entity->save($entity);
                }
                // Ajout du genre ou du style à l'album
                $album->{"add$field"}($entity);
            }
        }
    }

    /*
     * Permet d'ajouter un album aux favoris de l'utilisateur connecté
     */
    #[Route('/add-favorite', name: 'add_favorite', methods: ['POST'])]
    public function addFavorite(EntityManagerInterface $entityManager, Request $request, TracklistRepository $tracklistRepository, AlbumRepository $albumRepository, UserRepository $userRepository): JsonResponse
    {
        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);
        $albumId = $data['id'];

        // Si l'album n'est pas dans la base de données on le crée
        $album = $entityManager->getRepository(Album::class)->find($albumId) ?? (new Album())
            ->setId($albumId)
            ->setTitle($data['title'])
            ->setArtist($data['artists'][0]['name'])
            ->setYear($data['year'] ?? 0)
            ->setCountry($data['country'] ?? 'Unknown')
            ->setCoverImage($data['images'][0]['uri'] ?? 'https://m.media-amazon.com/images/I/61QbG3IAqlL._AC_UF350,350_QL80_.jpg')
            ->setDiscogLink($data['uri'])
            ->setLikes(1);

        // Ajout des genres et styles de l'album
        $this->updateRelationships($entityManager, $album, 'styles', Style::class, 'Style');
        $this->updateRelationships($entityManager, $album, 'genres', Genre::class, 'Genre');
        // Ajout des pistes de l'album
        if (isset($data['tracklist'])) {
            foreach ($data['tracklist'] as $track) {
                $tracklist = new Tracklist();
                $tracklist->setTitle($track['title']);
                if (isset($track['duration'])) {
                    $tracklist->setDuration($track['duration']);
                }
                $album->addTrack($tracklist);
                $tracklistRepository->save($tracklist);
            }
        }

        // Mise à jour de l'album dans la base de données
        $album->setLikes($album->getLikes() + 1);

        $user = $this->getUser();
        $user->addFavorite($album);

        $albumRepository->save($album);
        $userRepository->save($user);

        return new JsonResponse('success', 200);
    }


    /*
     * Permet de retirer un album des favoris de l'utilisateur connecté
     */
    #[Route('/remove-favorite', name: 'remove_favorite', methods: ['POST'])]
    public function removeFavorite(EntityManagerInterface $entityManager, Request $request, UserRepository $userRepository, AlbumRepository $albumRepository): JsonResponse
    {
        // Récupération des données de la requête
        $data = json_decode($request->getContent(), true);
        // Récupération de l'album
        $album = $entityManager->getRepository(Album::class)->find($data['id']);
        $user = $this->getUser();
        // Suppression de l'album des favoris de l'utilisateur
        $user->removeFavorite($album);
        $userRepository->save($user);
        $album->setLikes($album->getLikes() - 1);
        $albumRepository->save($album);
        return new JsonResponse('success', 200);
    }

    #[Route('/favorite', name: 'favorite')]
    public function favorite(): Response
    {
        $favorite = $this->getUser()->getFavorite();
        return $this->render('pages/favorite.html.twig', [
            'title' => 'Favorite',
            'favorite' => $favorite
        ]);
    }
}