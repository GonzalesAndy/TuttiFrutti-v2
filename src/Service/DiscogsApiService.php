<?php
namespace App\Service;

use App\Entity\Fruit;
use App\Repository\FruitRepository;
use Symfony\Component\HttpClient\HttpClient;

class DiscogsApiService
{
    private $baseUrl = 'https://api.discogs.com';

    private $fruitRepository;

    public function __construct(FruitRepository $fruitRepository)
    {
        $this->fruitRepository = $fruitRepository;
    }


    public function search($fruit, $type, $pagination = 1)
    {
        // $url = $this->baseUrl . '/database/search?q=' . $fruit . '&type=' . $type . '&page=' . $pagination;
        $url = $this->baseUrl . '/database/search';

        $httpClient = HttpClient::create();
        // Permet de récupérer les albums comportant le nom du fruit
        $response = $httpClient->request('GET', $url, [
            'query' => [
                'release_title' => $fruit,
                'type' => $type,
                'page' => $pagination
            ],
            'headers' => [
                'Authorization' => 'Discogs key=dTyvSehxTNlwNnVtQAlX, secret=XpzdPJFdIqfHjXqJPUuwsBGsHUXuQUCv',
            ],
        ]);

        return $response->toArray();
    }

    public function multipleLanguageSearch($fruitName, $type, $pagination = 1)
    {
        // Permet de rechercher tous les albums comportant le nom du fruit en plusieurs langues
        $fruit = $this->fruitRepository->findByName($fruitName);
        $searchResults = $this->search($fruit->getName()."||".$fruit->getJapanese()."||".$fruit->getEnglish(),$type, $pagination)['results'];
        return $searchResults;
    }

    public function getRelease($id)
    {
        // Permet de récupérer les informations d'un album en particulier
        $url = $this->baseUrl . '/masters/' . $id;

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Discogs key=dTyvSehxTNlwNnVtQAlX, secret=XpzdPJFdIqfHjXqJPUuwsBGsHUXuQUCv',
            ],
        ]);

        return $response->toArray();
    }

}
