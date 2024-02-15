<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;

class DiscogsApiService
{
    private $baseUrl = 'https://api.discogs.com';

    private $fruitsDict = array(
        'pomme' => array('りんご', 'pomme', 'apple'),
        'banane' => array('バナナ', 'banane'),
        'fraise' => array('いちご', 'fraise', 'strawberry'),
        'peche' => array('もも', 'pêche', 'peach'),
        'poire' => array('なし', 'poire', 'pear'),
        'framboise' => array('ラズベリー', 'framboise', 'raspberry'),
        'kiwi' => array('キウイ', 'kiwi', 'kiwi'),
        'grenade' => array('ざくろ', 'grenade', 'pomegranate')
    );


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

    public function multipleLanguageSearch($query, $type)
    {
        if (array_key_exists($query, $this->fruitsDict)) {
            $query = $this->fruitsDict[$query];
        }
        for ($i = 0; $i < count($query); $i++) {
            // concatenate the results of each search
            $result['results'] = $this->search($query[$i], $type)['results'];

        }
        return $result['results'];
    }
}
