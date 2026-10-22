<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\AvisRepository;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ORM\Table(name: '`avis`')]    
class Avis {
    #[ORM\Id]  
    #[ORM\Column(type:"integer")]
    private ?int $avis_id = null;

    #[ORM\Column(type:"string",length: 50)]
    private ?string $note = null;

    #[ORM\Column(type:"string",length: 50)]
    private ?string $description = null;

    #[ORM\Column(type:"string",length: 50,nullable: true)]
    private ?string $statut = null;

    #[ORM\OneToMany(targetEntity: Publie::class, mappedBy: 'avis_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $publie;

    public function __construct() {
        $this->publie = new ArrayCollection();
    }

    public function getAvisId(): ?int
    {
        return $this->avis_id;
    }

    public function setAvisId(int $avis_id): static
    {
        $this->avis_id = $avis_id;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }
    /**
    * @return Collection<int, Publie>
    */  
    public function getPublies(): Collection
    {
        return $this->publie;
    }
    
    public function addPublie(Publie $publie): static
    {
        if (!$this->publie->contains($publie)) {
            $this->publie->add($publie);
            $publie->setAvisId($this);
          }

        return $this;
    }

    public function removePublie(Publie $publie): static
    {
        if ($this->publie->removeElement($publie)) {
            // set the owning side to null (unless already changed)
            if ($publie->getAvisId() === $this) {
                $publie->setAvisId(null);
            }
        }

        return $this;
    }

}