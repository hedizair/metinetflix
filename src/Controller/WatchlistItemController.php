<?php

namespace App\Controller;

use App\Entity\WatchlistItem;
use App\Repository\WatchlistItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use App\Repository\WatchlistRepository;
use App\Repository\MovieRepository;
use App\Repository\SerieRepository;


#[Route('/watchlist/item')]
class WatchlistItemController extends AbstractController
{
    #[Route('/new/{type}/{idTMDB}', name: 'app_watchlist_item_new', methods: ['GET', 'POST'])]
    public function new(String $type, Int $idTMDB): Response
    {
        if($type == "movie")
        {
            return $this->redirectToRoute('app_movie_new', ['idTMDB' => $idTMDB]);
        }
        else if($type == "serie")
        {
            return $this->redirectToRoute('app_serie_new', ['idTMDB' => $idTMDB]);
        }
        else
        {
            return $this->redirect('/');
        }
    }

    #[Route('/add/{type}/{idEntity}', name: 'app_watchlist_item_add', methods: ['GET', 'POST'])]
    public function add(String $type,
                        Int $idEntity,
                        WatchlistRepository $watchlistRepository,
                        WatchlistItemRepository $watchlistItemRepository,
                        movieRepository $movieRepository,
                        serieRepository $serieRepository
    ): Response
    {
        //Watchlist find request
        $watchlist = $watchlistRepository->findOneBy(['id' => 1]);

        //WatchlistItem creation
        $watchlistItem = $watchlistItemRepository->findOneBy([$type => $idEntity]);

        if(!isset($watchlistItem)) {
            $watchlistItem = new WatchlistItem();
            $watchlistItem->setWatchlist($watchlist);
            $watchlistItem->setItemType($type);

            if($type == "movie") {
                $movie = $movieRepository->find($idEntity);
                $watchlistItem->setMovie($movie);
            }
            else if($type == "serie")
            {
                $serie = $serieRepository->find($idEntity);
                $watchlistItem->setSerie($serie);
            }

            $watchlistItemRepository->save($watchlistItem, true);
        }

        return $this->redirectToRoute('app_watchlist_show', ['id' => $watchlist->getId()]);
    }

    #[Route('/{id}', name: 'app_watchlist_item_delete', methods: ['POST'])]
    public function delete(Request $request, WatchlistItem $watchlistItem, WatchlistItemRepository $watchlistItemRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$watchlistItem->getId(), $request->request->get('_token'))) {
            $watchlistItemRepository->remove($watchlistItem, true);
        }

        return $this->redirectToRoute('app_watchlist_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
