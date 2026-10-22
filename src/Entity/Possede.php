<?php

namespace App\Entity;

use App\Repository\PossedeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: PossedeRepository::class)]
#[ORM\Table(name: '`possede`')]     
#[UniqueEntity(fields: ['utilisateur_id'], message: 'Cet utilisateur possède déjà ce rôle.')]
class Possede { 
    #[ORM\Id]    
    #[ORM\OneToOne(targetEntity: Utilisateur::class, inversedBy: 'possede')]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id", onDelete: "CASCADE")]
    private ?Utilisateur $utilisateur_id = null;

    #[ORM\ManyToOne(targetEntity: Role::class, inversedBy: 'possede')]
    #[ORM\JoinColumn(name: "role_id", referencedColumnName: "role_id", onDelete: "CASCADE")]
    private ?Role $role_id = null;  

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?Utilisateur $utilisateur_id): static
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

    public function getRoleId(): ?Role
    {
        return $this->role_id;
    }

    public function setRoleId(?Role $role_id): static
    {
        $this->role_id = $role_id;

        return $this;
    }
}
