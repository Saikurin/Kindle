<?php

namespace App\Entity;

use App\Repository\IndexRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: IndexRepository::class)]
#[ORM\Table(name: '`indexes`', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'token', columns: ['token']),
])]
#[UniqueEntity(fields: ['token'], message: 'This token is already in use.')]
class Index
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255,unique: true)]
    private ?string $token = null;

    #[ORM\Column]
    private array $metadata = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

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
}
