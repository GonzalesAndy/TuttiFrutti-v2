<?php

namespace App\Entity;

use App\Repository\TracklistRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TracklistRepository::class)]
class Tracklist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'track')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Album $id_album = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $duration = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdAlbum(): ?Album
    {
        return $this->id_album;
    }

    public function setIdAlbum(?Album $id_album): static
    {
        $this->id_album = $id_album;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDuration(): ?string
    {
        return $this->duration;
    }

    public function setDuration(?string $duration): static
    {
        $this->duration = $duration;

        return $this;
    }
}
