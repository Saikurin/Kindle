<?php

namespace App\Entity;

use App\Repository\BookRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookRepository::class)]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(unique: true)]
    private ?int $gut_id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column]
    private array $metadata = [];

    #[ORM\Column]
    private ?bool $alreadyIndex = null;

    #[ORM\Column(nullable: true)]
    private ?float $score = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGutId(): ?int
    {
        return $this->gut_id;
    }

    public function setGutId(int $gut_id): self
    {
        $this->gut_id = $gut_id;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function isAlreadyIndex(): ?bool
    {
        return $this->alreadyIndex;
    }

    public function setAlreadyIndex(bool $alreadyIndex): self
    {
        $this->alreadyIndex = $alreadyIndex;

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(?float $score): self
    {
        $this->score = $score;

        return $this;
    }
}
