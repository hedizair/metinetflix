<?php

namespace App\Service;

use App\Entity\Card;
use Symfony\Component\HttpClient\HttpClient;

class SerieParsing
{
    public function popularParsing(int $page): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/tv/popular?api_key=' . $apiKey . '&language=fr-FR&page='.$page);
        $items = $response->toArray();

        $series = array();
        foreach ($items['results'] as $item) {
            $card = new Card(
                $item['id'],
                $item['name'],
                $item['first_air_date'],
                'https://image.tmdb.org/t/p/original/' . $item['poster_path'],
            'app_serie_show');
            $series[] = $card;
        }

        return $series;
    }

    public function serieParsing(int $id): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/tv/'.$id.'?api_key=' . $apiKey . '&language=fr-FR');
        $item = $response->toArray();

        $overview = !empty($item['overview']) ? $item['overview'] : "Aucune description";

        $genres = array();
        foreach($item['genres'] as $genre) {
            array_push($genres, $genre['name']);
        }
        if(count($genres) == 0) array_push($genres, "Aucun genre");

        $creators = array();
        foreach($item['created_by'] as $creator) {
            array_push($creators, $creator['name']);
        }
        if(count($creators) == 0) array_push($genres, "Aucun créateur");

        $serie = array(
            'id' => $item['id'],
            'name' => $item['name'],
            'backdrop_path' => 'https://image.tmdb.org/t/p/original/' . $item['backdrop_path'],
            'original_name' => $item['original_name'],
            'original_language' => $item['original_language'],
            'first_air_date' => $item['first_air_date'],
            'last_air_date' => $item['last_air_date'],
            'number_of_seasons' => $item['number_of_seasons'],
            'number_of_episodes' => $item['number_of_episodes'],
            'overview' => $overview,
            'genres' => $genres,
            'creators' => $creators);

        return $serie;
    }
}