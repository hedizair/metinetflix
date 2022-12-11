<?php

namespace App\Service;

use App\Entity\Card;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpClient\HttpClient;

class MovieParsing
{
    public function popularParsing(int $page): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/popular?api_key='.$apiKey.'&language=fr-FR&page='.$page);
        $items = $response->toArray();

        $movies = array();
        foreach($items['results'] as $item) {
            $card = new Card(
                $item['id'],
                $item['title'],
                $item['release_date'],
                'https://image.tmdb.org/t/p/original/' . $item['poster_path'],
                'app_movie_show',
                'movie');
            $movies[] = $card;
        }

        return $movies;
    }

    public function upcomingParsing(int $page): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/upcoming?api_key='.$apiKey.'&language=fr-FR&page='.$page);
        $items = $response->toArray();

        $movies = array();
        foreach($items['results'] as $item) {
            $card = new Card(
                $item['id'],
                $item['title'],
                $item['release_date'],
                'https://image.tmdb.org/t/p/original/' . $item['poster_path'],
                'app_movie_show',
                'movie');
            $movies[] = $card;
        }

        return $movies;
    }

    public function queryParsing(int $page, string $query): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/movie?api_key='.$apiKey.'&language=en-US&query='.$query.'&page=1&include_adult=false');
        $items = $response->toArray();

        $movies = array();
        foreach($items['results'] as $item) {
            $card = new Card(
                $item['id'],
                $item['title'],
                $item['release_date'],
                'https://image.tmdb.org/t/p/original/' . $item['poster_path'],
                'app_movie_show',
                'movie');
            $movies[] = $card;
        }



        return $movies;
    }

    public function movieParsing(int $id): array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client->request('GET', 'https://api.themoviedb.org/3/movie/'.$id.'?api_key='.$apiKey.'&language=fr-FR');
        $item = $response->toArray();

        $overview = !empty($item['overview']) ? $item['overview'] : "Aucune description";

        $genres = array();
        foreach($item['genres'] as $genre) {
            array_push($genres, $genre['name']);
        }
        if(count($genres) == 0) array_push($genres, "Aucun genre");

        $movie = array(
            'id' => $item['id'],
            'title' => $item['title'],
            'backdrop_path' => 'https://image.tmdb.org/t/p/original/' . $item['backdrop_path'],
            'original_title' => $item['original_title'],
            'original_language' => $item['original_language'],
            'release_date' => $item['release_date'],
            'runtime' => $item['runtime'],
            'overview' => $overview,
            'genres' => $genres,
            'type' => 'movie');

        return $movie;
    }
}