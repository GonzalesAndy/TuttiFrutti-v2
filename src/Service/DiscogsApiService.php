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


    public function search($fruit, $type)
    {
        $url = $this->baseUrl . '/database/search';

        $httpClient = HttpClient::create();

        $response = $httpClient->request('GET', $url, [
            'query' => [
                'release_title' => $fruit,
                'type' => $type,
            ],
            'headers' => [
                'Authorization' => 'Discogs key=dTyvSehxTNlwNnVtQAlX, secret=XpzdPJFdIqfHjXqJPUuwsBGsHUXuQUCv',
            ],
        ]);

        return $response->toArray();
    }

    public function getTracklist($id)
    {
        $url = $this->baseUrl . '/masters/' . $id;

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url, [
            'headers' => [
                'Authorization' => 'Discogs key=dTyvSehxTNlwNnVtQAlX, secret=XpzdPJFdIqfHjXqJPUuwsBGsHUXuQUCv',
            ],
        ]);

        $tracklist = $response->toArray()['tracklist'];

        return $tracklist;
    }

    public function multipleLanguageSearch($fruitName, $type)
    {
        $fruit = $this->fruitRepository->findByName($fruitName);
        $searchResults = $this->search($fruit->getName()."||".$fruit->getJapanese()."||".$fruit->getEnglish(),$type)['results'];
        return $searchResults;
    }

    public function getRelease($id)
    {
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
