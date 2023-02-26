<?php

namespace App\Controller;

use App\Repository\BookRepository;
use App\Repository\IndexRepository;
use App\Repository\TreeRepository;
use App\Service\JaccardService;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TokenController extends AbstractController
{
    /**
     * @param IndexRepository $repository
     * @return JsonResponse
     */
    #[Route('/words', name: 'app_words')]
    public function words(IndexRepository $repository): JsonResponse
    {
        return $this->json(
            array_map('current', $repository->getTokens())
        );
    }

    #[Route('/word/{word}', name: 'app_word')]
    public function word(string $word, IndexRepository $repository, BookRepository $bookRepository): JsonResponse
    {
        $books = $bookRepository->findAllAsArray();
        $token = $repository->findOneBy(['token' => $word]);
        $data = [];
        if($token) {
            foreach ($token->getMetadata() as $meta) {
                $booksUsed = array_values(array_filter($books, static function ($book) use ($meta) {
                    return $book['gut_id'] === $meta['id'];
                }));
                $data[] = (array)$booksUsed[0];
            }
        }
        usort($data, static function ($item1, $item2) {
            return $item2['score'] <=> $item1['score'];
        });
        return $this->json(
            $data
        );
    }

    /**
     * @param string $regexp
     * @param IndexRepository $repository
     * @param Request $request
     * @param BookRepository $bookRepository
     * @return JsonResponse
     */
    #[Route('/search-regexp/{regexp}', name: 'app_search_regexp')]
    public function searchRegexp(string $regexp, IndexRepository $repository, Request $request, BookRepository $bookRepository)
    {
        // Recupere la clé regexp envoyé en POST depuis la request
        $regexp = urldecode($regexp);
        //$regexp = $request->toArray()['regexp'];

        // Recherche les tokens qui correspondent à la regexp
        $tokens = $repository->searchRegexp($regexp);
        $books = $bookRepository->findAllAsArray();
        $data = [];

        foreach ($tokens as $token) {
            foreach ($token['metadata'] as $meta) {
                $booksUsed = array_values(array_filter($books, static function ($book) use ($meta) {
                    return $book['gut_id'] === $meta['id'];
                }));
                $data[] = (array)$booksUsed[0];
            }
        }

        $data = array_unique($data, SORT_REGULAR);

        return $this->json(array_values($data));
    }
}
