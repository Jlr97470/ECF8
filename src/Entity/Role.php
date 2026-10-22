<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use App\Repository\RoleRepository;

#[ORM\Entity(repositoryClass: RoleRepository::class)]
#[ORM\Table(name: '`role`')]    
#[UniqueEntity(fields: ['libelle'], message: 'Le nom du rôle existe déjà')]
class Role {
    #[ORM\Id]  
    #[ORM\Column(type:"integer")]
    private ?int $role_id = null;

    #[ORM\Column(type:"string",length: 50, unique: true,nullable:false)]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: Possede::class, mappedBy: 'role_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $possede;

    public function __construct() {
        $this->possede = new ArrayCollection();
    }

    public function getRoleId(): ?int
    {
        return $this->role_id;
    }

    public function setRoleId(int $role_id): static
    {
        $this->role_id = $role_id;

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
     * @return Collection<int, Possede>
     */
    public function getPossedes(): Collection
    {
        return $this->possede;
    }

    public function addPossede(Possede $possede): static {
        if (!$this->possede->contains($possede)) {
            $this->possede->add($possede);
            $possede->setRoleId($this);
        }

        return $this;
    }

    public function removePossede(Possede $possede): static {
        if ($this->possede->removeElement($possede)) {
            // set the owning side to null (unless already changed)
            if ($possede->getRoleId() === $this) {
                $possede->setRoleId(null);
            }
        }

        return $this;
    }    
}