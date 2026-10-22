<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\PlatRepository;

#[ORM\Entity(repositoryClass: PlatRepository::class)]
#[ORM\Table(name: '`plat`')]    
class Plat {
    #[ORM\Id]  
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $plat_id = null;

    #[ORM\Column(type:"string",length: 50)]
    private ?string $titre_plat = null;

    #[ORM\Column(type:"blob")]
    private $photo = null;
    private ?string $file= null;

    #[ORM\OneToMany(targetEntity: ProposePlat::class, mappedBy: 'plat_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $proposeplat;

    #[ORM\OneToMany(targetEntity: Contient::class, mappedBy: 'plat_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $contient;     

    public function __construct()
    {
        $this->proposeplat = new ArrayCollection();
        $this->contient = new ArrayCollection();
    }

    public function getPlatId(): ?int
    {
        return $this->plat_id;
    }

    public function setPlatId(int $plat_id): static
    {
        $this->plat_id = $plat_id;

        return $this;
    }

    public function getTitrePlat(): ?string
    {
        return $this->titre_plat;
    }

    public function setTitrePlat(string $titre_plat): static
    {
        $this->titre_plat = $titre_plat;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto($photo): static
    {
        $this->photo = $photo;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile($file): static
    {
        $this->file = $file;

        return $this;
    }
    
    public function getProposeplats(): Collection
    {
        return $this->proposeplat;
    }

    public function addProposeplat(ProposePlat $proposeplat): static
    {
        if (!$this->proposeplat->contains($proposeplat)) {
            $this->proposeplat->add($proposeplat);
            $proposeplat->setPlatId($this);
        }

        return $this;
    }

    public function removeProposeplat(ProposePlat $proposeplat): static
    {
        if ($this->proposeplat->removeElement($proposeplat)) {
            // set the owning side to null (unless already changed)
            if ($proposeplat->getPlatId() === $this) {
                $proposeplat->setPlatId(null);
            }
        }

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
            $contient->setPlatId($this);
        }

        return $this;
    }

    public function removeContient(Contient $contient): static
    {
        if ($this->contient->removeElement($contient)) {
            // set the owning side to null (unless already changed)
            if ($contient->getPlatId() === $this) {
                $contient->setPlatId(null);
            }
        }

        return $this;
    }
}   