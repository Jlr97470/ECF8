<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\RegimeRepository;

#[ORM\Entity(repositoryClass: RegimeRepository::class)]
#[ORM\Table(name: '`regime`')]    
class Regime {
    #[ORM\Id]  
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $regime_id = null;

    #[ORM\Column(type:"string",length: 50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: Adapte::class, mappedBy: 'regime_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $adaptes;

     public function __construct()
    {
        $this->adaptes = new ArrayCollection();
    }

    public function getRegimeId(): ?int
    {
        return $this->regime_id;
    }

    public function setRegimeId(int $regime_id): static
    {
        $this->regime_id = $regime_id;

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
     * @return Collection<int, Adapte>
     */
    public function getAdaptes(): Collection
    {
        return $this->adaptes;
    }

    public function addAdapte(Adapte $adapte): static
    {
        if (!$this->adaptes->contains($adapte)) {
            $this->adaptes->add($adapte);
            $adapte->setRegimeId($this);
        }

        return $this;
    }

    public function removeAdapte(Adapte $adapte): static
    {
        if ($this->adaptes->removeElement($adapte)) {
            // set the owning side to null (unless already changed)
            if ($adapte->getRegimeId() === $this) {
                $adapte->setRegimeId(null);
            }
        }

        return $this;
    }
}  