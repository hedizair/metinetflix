<?php

namespace App\Service;

use App\Entity\Card;
use mysql_xdevapi\Warning;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\HttpClient\HttpClient;
use function PHPUnit\Framework\isEmpty;
use function PHPUnit\Framework\isNull;

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
        $response = $client->request('GET', 'https://api.themoviedb.org/3/search/movie?api_key='.$apiKey.'&language=en-US&query='.$query.'&page='.$page.'&include_adult=false');
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


        $belongs_to_collection = new Card(
         $item['belongs_to_collection']['id'] ?? 0,
         $item['belongs_to_collection']['name'] ?? 'Aucune',
         $item['belongs_to_collection']['release_date'] ?? 0,
         'https://image.tmdb.org/t/p/original/' . ($item['belongs_to_collection']['backdrop_path'] ?? ""),
         'app_saga_show',
         'saga' );

        $movie = array(
            'id' => $item['id'],
            'title' => $item['title'],
            'backdrop_path' => 'https://image.tmdb.org/t/p/original/' . $item['backdrop_path'],
            'poster_path' => 'https://image.tmdb.org/t/p/original/' . $item['poster_path'],
            'original_title' => $item['original_title'],
            'original_language' => $item['original_language'],
            'release_date' => $item['release_date'],
            'runtime' => $item['runtime'],
            'overview' => $overview,
            'genres' => $genres,
            'belongs_to_collection' => $belongs_to_collection,
            'type' => 'movie');

        return $movie;
    }

    public function sortParsing(int $page, String $sortBy):array
    {
        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';

        $client = HttpClient::create();
        $response = $client
            ->request(
                'GET',
                'https://api.themoviedb.org/3/discover/movie?api_key='.$apiKey.'&language=fr-FR&sort_by='.$sortBy.'&include_adult=false&include_video=false&page='.$page.'&with_watch_monetization_types=flatrate'
            );
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

    public function queryMaker(int $page, array $filters = null, string $sortBy = null ):array
    {


        //  https://api.themoviedb.org/3/discover/movie?api_key=<<api_key>>&language=en-US&sort_by=release_date.asc&include_adult=false&include_video=false&page=1&primary_release_date.gte=1914-02-12&primary_release_date.lte=2002-18-10&with_watch_monetization_types=flatrate

        $apiKey = '357ffc10ea12b3e3226406719d3f9fe5';
        $query =  'https://api.themoviedb.org/3/discover/movie?api_key='.$apiKey.'&language=fr-FR&page='.$page;
        $keysFilters = array_keys($filters);
        if(!is_null($filters) && !is_null($sortBy) ){

            $query .= '&sort_by=' . $this->formateSortBy($sortBy);
            foreach ($keysFilters as $keyFilter){

                if(gettype($filters[$keyFilter]) === 'object'){
                    $query .= '&'.$keyFilter.'='.$filters[$keyFilter]->format('Y-m-d');
                }else if($keyFilter === 'include_adult'){
                    $query .= '&'.$keyFilter.'=';
                    $query .= $filters[$keyFilter] ? 'true' : 'false';
                }

            }
        }else if(is_null($filters)){
            $query .= '&sort_by=' . $this->formateSortBy($sortBy);
        }



        $client = HttpClient::create();
        $response = $client
            ->request(
                'GET',
                $query
            );
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


    function formateSortBy(string $sortBy): string
    {
        if($sortBy === 'date.asc'){
            return 'release_date.asc';
        }elseif($sortBy === 'date.desc'){
            return 'release_date.desc';
        }
        return $sortBy;

    }
}