<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $ShortUrl = null;

    #[ORM\Column(length: 255)]
    private ?string $LongUrl = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShortUrl(): ?string
    {
        return $this->ShortUrl;
    }

    public function setShortUrl(string $ShortUrl): static
    {
        $this->ShortUrl = $ShortUrl;

        return $this;
    }

    public function getLongUrl(): ?string
    {
        return $this->LongUrl;
    }

    public function setLongUrl(string $LongUrl): static
    {
        $this->LongUrl = $LongUrl;

        return $this;
    }
}
