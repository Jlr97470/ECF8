<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\AllergeneRepository;


#[ORM\Entity(repositoryClass: AllergeneRepository::class)]
#[ORM\Table(name: '`allergene`')]    
class Allergene {
    #[ORM\Id]  
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $allergene_id = null;

    #[ORM\Column(type:"string",length: 50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: Contient::class, mappedBy: 'allergene_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $contient;      

    public function __construct()
    {
        $this->contient = new ArrayCollection();
    }
    public function getAllergeneId(): ?int
    {
        return $this->allergene_id;
    }

    public function setAllergeneId(int $allergene_id): static
    {
        $this->allergene_id = $allergene_id;

        return $this;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): static
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * @return Collection<int, Contient>
     */
    public function getContients(): Collection
    {
        return $this->contient;
    }

    public function addContient(Contient $contient): static
    {
        if (!$this->contient->contains($contient)) {
            $this->contient->add($contient);
            $contient->setAllergeneId($this);
        }

        return $this;
    }

    public function removeContient(Contient $contient): static
    {
        if ($this->contient->removeElement($contient)) {
            // set the owning side to null (unless already changed)
            if ($contient->getAllergeneId() === $this) {
                $contient->setAllergeneId(null);
            }
        }

        return $this;
    }
}   