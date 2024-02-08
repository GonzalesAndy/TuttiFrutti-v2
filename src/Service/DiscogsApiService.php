<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class DiscogsApiService
{
    private $baseUrl = 'https://api.discogs.com';

    public function search($query, $type)
    {
        $url = $this->baseUrl . '/database/search';

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', $url, [
            'query' => [
                'release_title' => $query,
                'type' => $type,
            ],
            'headers' => [
                'Authorization' => 'Discogs key=dTyvSehxTNlwNnVtQAlX, secret=XpzdPJFdIqfHjXqJPUuwsBGsHUXuQUCv',
            ],
        ]);

        return $response->toArray();
    }
}