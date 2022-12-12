<?php

namespace App\Service;


use App\Entity\Card;
use Symfony\Component\HttpClient\HttpClient;

class SagaParsing
{
    public function sagaParsing(int $id): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/collection/'.$id.'?api_key='.$apiKey.'&language=fr-FR');
        $item = $response->toArray();

        $overview = !empty($item['overview']) ? $item['overview'] : "Aucune description";

        $movies = array();
        foreach($item['parts'] as $movie) {
            $card = new Card(
                $movie['id'],
                $movie['title'],
                $movie['release_date'] ?? "",
                'https://image.tmdb.org/t/p/original/' . $movie['poster_path'],
                'app_movie_show',
                'movie');
            $movies[] = $card;
        }

        $saga = array(
            'id' => $item['id'],
            'name' => $item['name'],
            'backdrop_path' => 'https://image.tmdb.org/t/p/original/' . $item['backdrop_path'],
            'overview' => $overview,
            'movies' => $movies,
            'type' => 'saga');

        return $saga;
    }
}