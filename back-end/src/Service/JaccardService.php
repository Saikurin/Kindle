<?php

namespace App\Service;

use App\Entity\Book;

class JaccardService
{

    private $books;

    public function __construct(array $books)
    {
        $this->books = $books;
    }

    public function getBooksOrdered()
    {
        /*
         *
         */
        $centralities = [];

        /**
         * @var int $id
         * @var Book $book
         */
        foreach ($this->books as $id => $book) {
            $centrality = 0;
            /** @var Book $otherBook */
            foreach ($this->books as $otherBook) {
                if ($book->getGutId() !== $otherBook->getGutId()) {
                    $jaccard = $this->jaccardIndex($book->words, $otherBook->words);
                    $centrality += $jaccard;
                }
            }

            $centralities[$id] = $centrality;
        }
        // Trie les tableaux par centralité décroissante
        arsort($centralities);

        $booksOrdered = [];

        foreach ($centralities as $id => $centrality) {
            $booksOrdered[] = $this->books[$id];
        }

        return $booksOrdered;
    }

    private function jaccardIndex(mixed $words, mixed $words1)
    {
        $set1 = array_flip($words);
        $set2 = array_flip($words1);

        $intersection = array_intersect_key($set1, $set2);
        $union = $set1 + $set2;

        return count($intersection) / count($union);
    }


}
