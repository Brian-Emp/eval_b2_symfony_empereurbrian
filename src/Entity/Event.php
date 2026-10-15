<?php

namespace App\Entity;

use App\Repository\EventRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EventRepository::class)]
class Event
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    //validator
    #[Assert\NotBlank]
    private ?string $titre = null;

    #[ORM\Column]
    //validator
    #[Assert\NotBlank]
    private ?\DateTime $date = null;

    #[ORM\Column(type: Types::TEXT)]
    //validator
    #[Assert\NotBlank]
    private ?string $informations = null;

    #[ORM\Column(length: 50)]
    private ?string $categorie = null;

    #[ORM\Column]
    //validator
    #[Assert\Positive]
    private ?int $places = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column]
    private ?bool $valide = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'evenements')]
    private Collection $inscrits;

    public function __construct()
    {
        $this->inscrits = new ArrayCollection();
        // set valide faux par defaut
        $this->valide = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDate(): ?\DateTime
    {
        return $this->date;
    }

    public function setDate(\DateTime $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getInformations(): ?string
    {
        return $this->informations;
    }

    public function setInformations(string $informations): static
    {
        $this->informations = $informations;

        return $this;
    }

    public function getCategorie(): ?string
    {
        return $this->categorie;
    }

    public function setCategorie(string $categorie): static
    {
        $this->categorie = $categorie;

        return $this;
    }

    public function getPlaces(): ?int
    {
        return $this->places;
    }

    public function setPlaces(int $places): static
    {
        $this->places = $places;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): static
    {
        $this->valide = $valide;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getInscrits(): Collection
    {
        return $this->inscrits;
    }

    public function addInscrit(User $inscrit): static
    {
        if (!$this->inscrits->contains($inscrit)) {
            $this->inscrits->add($inscrit);
        }

        return $this;
    }

    public function removeInscrit(User $inscrit): static
    {
        $this->inscrits->removeElement($inscrit);

        return $this;
    }

    //gestion du nombre de places
    public function getPlacesRestantes(): int
    {
        return $this->places - count($this->inscrits);
    }

    public function isComplet(): bool
    {
        return count($this->inscrits) >= $this->places;
    }
}
