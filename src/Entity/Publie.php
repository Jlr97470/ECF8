<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use App\Repository\PublieRepository;

#[ORM\Entity(repositoryClass: PublieRepository::class)]
#[ORM\Table(name: '`publie`')]     
#[UniqueEntity(fields: ['utilisateur_id'], message: 'Cet utilisateur a déjà publié un avis.')]
class Publie { 
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Utilisateur::class, inversedBy: 'publie')]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id", onDelete: "CASCADE")]
    private ?Utilisateur $utilisateur_id = null;

    #[ORM\ManyToOne(targetEntity: Avis::class,inversedBy: 'publie')]
    #[ORM\JoinColumn(name: "avis_id", referencedColumnName: "avis_id", onDelete: "CASCADE")]
    private ?Avis $avis_id = null;  

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?Utilisateur $utilisateur_id): static
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

    public function getAvisId(): ?Avis
    {
        return $this->avis_id;
    }

    public function setAvisId(?Avis $avis_id): static
    {
        $this->avis_id = $avis_id;

        return $this;
    }
}