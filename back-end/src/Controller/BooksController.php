<?php

namespace App\Controller;

use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/books')]
class BooksController extends AbstractController
{
    #[Route('/', name: 'app_books')]
    public function books(BookRepository $repository, SerializerInterface $serializer): JsonResponse
    {
        $models = $repository->findAllAsArray();
        $data = $serializer->serialize($models, JsonEncoder::FORMAT);
        return new JsonResponse($data, 200, [], true);
    }

    #[Route('/{id}', name: 'app_book')]
    public function book(string $id, BookRepository $repository, Request $request): JsonResponse
    {
        $book = $repository->findOneBy(['gut_id' => $id]);
        if($book) {
            $model = $book->getMetadata();
        }
        return new JsonResponse($model, 200, [], false);
    }
}
