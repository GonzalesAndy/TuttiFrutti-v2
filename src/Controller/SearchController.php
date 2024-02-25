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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends AbstractController
{
    /*
     * Permet d'afficher les résultats de recherche pour 3 fruits pris aléatoirement
     */
    #[Route('/explorer', name: 'explorer')]
    public function explorer(DiscogsApiService $discogsApiService, EntityManagerInterface $entityManager): Response
    {
        // Récupération de 3 fruits aléatoires
        $results = [];
        $descriptions = [];
        $fruits = $entityManager->getRepository(className: Fruit::class)->findAll();
        shuffle($fruits);
        $fruits = array_slice($fruits, 0, 3);

        // Recherche d'albums en utilisant l'api discogs sur ces fruits
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
        // Si l'album est déjà dans la base de données, on le récupère, sinon on le récupère depuis l'api discogs
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
                    'duration' => $track->getDuration()])->toArray(),
                'genres' => $album->getGenre()->map(fn(Genre $genre) => $genre->getGenre())->toArray(),
                'styles' => $album->getStyle()->map(fn(Style $style) => $style->getStyle())->toArray(),
            ];
            // Vérification si l'utilisateur est connecté et si l'album est dans ses favoris
            if ($this->getUser()) {
                $result['liked'] = $this->getUser()->getFavorite()->contains($album);
            }
        // Si l'album n'est pas dans la base de données, on le récupère depuis l'api discogs
        } else {
            $result = $discogsApiService->getRelease($id);
            $result['liked'] = false;
        }
        return $this->render('pages/show.html.twig', [
            'title' => 'Explorer',
            'result' => $result
        ]);
    }


}