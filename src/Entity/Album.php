<?php

namespace App\Entity;

use App\Repository\AlbumRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AlbumRepository::class)]
class Album
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 100)]
    private ?string $artist = null;

    #[ORM\Column]
    private ?int $year = null;

    #[ORM\Column(length: 100)]
    private ?string $country = null;

    #[ORM\Column(length: 510, nullable: true)]
    private ?string $cover_image = null;

    #[ORM\Column(length: 510)]
    private ?string $discog_link = null;

    #[ORM\Column]
    private ?int $likes = null;

    #[ORM\ManyToMany(targetEntity: Format::class, inversedBy: 'albums')]
    private Collection $format;

    #[ORM\ManyToMany(targetEntity: Genre::class, inversedBy: 'albums')]
    private Collection $genre;

    #[ORM\ManyToMany(targetEntity: Style::class, inversedBy: 'albums')]
    private Collection $style;

    public function __construct()
    {
        $this->format = new ArrayCollection();
        $this->genre = new ArrayCollection();
        $this->style = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getArtist(): ?string
    {
        return $this->artist;
    }

    public function setArtist(string $artist): static
    {
        $this->artist = $artist;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): static
    {
        $this->year = $year;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): static
    {
        $this->country = $country;

        return $this;
    }

    public function getCoverImage(): ?string
    {
        return $this->cover_image;
    }

    public function setCoverImage(?string $cover_image): static
    {
        $this->cover_image = $cover_image;

        return $this;
    }

    public function getDiscogLink(): ?string
    {
        return $this->discog_link;
    }

    public function setDiscogLink(string $discog_link): static
    {
        $this->discog_link = $discog_link;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;

        return $this;
    }

    /**
     * @return Collection<int, Format>
     */
    public function getFormat(): Collection
    {
        return $this->format;
    }

    public function addFormat(Format $format): static
    {
        if (!$this->format->contains($format)) {
            $this->format->add($format);
        }

        return $this;
    }

    public function removeFormat(Format $format): static
    {
        $this->format->removeElement($format);

        return $this;
    }

    /**
     * @return Collection<int, Genre>
     */
    public function getGenre(): Collection
    {
        return $this->genre;
    }

    public function addGenre(Genre $genre): static
    {
        if (!$this->genre->contains($genre)) {
            $this->genre->add($genre);
        }

        return $this;
    }

    public function removeGenre(Genre $genre): static
    {
        $this->genre->removeElement($genre);

        return $this;
    }

    /**
     * @return Collection<int, Style>
     */
    public function getStyle(): Collection
    {
        return $this->style;
    }

    public function addStyle(Style $style): static
    {
        if (!$this->style->contains($style)) {
            $this->style->add($style);
        }

        return $this;
    }

    public function removeStyle(Style $style): static
    {
        $this->style->removeElement($style);

        return $this;
    }
}
