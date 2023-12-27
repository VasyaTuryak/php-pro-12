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

    #[ORM\Column(nullable: true)]
    private ?int $RedirectNumber = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $CreatedAt;

    #[ORM\Column(length: 180, unique: false, nullable: false)]
    private ?string $email = null;

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

    public function getRedirectNumber(): ?int
    {
        return $this->RedirectNumber;
    }

    public function setRedirectNumber(?int $RedirectNumber): static
    {
        $this->RedirectNumber = $RedirectNumber;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->CreatedAt;
    }

    public function setCreatedAt(\DateTimeImmutable $CreatedAt): static
    {
        $this->CreatedAt = $CreatedAt;

        return $this;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }


}
