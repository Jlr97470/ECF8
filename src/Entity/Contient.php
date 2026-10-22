<?php

namespace App\Entity;

use App\Repository\ContientRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ContientRepository::class)]
#[ORM\Table(name: '`contient`')]     
#[UniqueEntity(fields: ['plat_id', 'allergene_id'], message: 'Cette combinaison de plat et d\'allergène existe déjà')]
class Contient { 
    #[ORM\Id]    
    #[ORM\ManyToOne(targetEntity: Plat::class, inversedBy: "contients")]
    #[ORM\JoinColumn(name: "plat_id", referencedColumnName: "plat_id", onDelete: "CASCADE")]
    private ?Plat $plat_id = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Allergene::class, inversedBy: "contients" )]
    #[ORM\JoinColumn(name: "allergene_id", referencedColumnName: "allergene_id", onDelete: "CASCADE")]
    private ?Allergene $allergene_id = null;  

    public function getplatId(): ?plat
    {
        return $this->plat_id;
    }

    public function setplatId(?plat $plat_id): static
    {
        $this->plat_id = $plat_id;

        return $this;
    }

    public function getAllergeneId(): ?Allergene
    {
        return $this->allergene_id;
    }

    public function setAllergeneId(?Allergene $allergene_id): static
    {
        $this->allergene_id = $allergene_id;

        return $this;
    }
}