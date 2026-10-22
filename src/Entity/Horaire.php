<?php

namespace App\Entity;

use App\Repository\HoraireRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HoraireRepository::class)]
#[ORM\Table(name: '`horaire`')]    
class Horaire {
    #[ORM\Id]  
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $horaire_id = null;

    #[ORM\Column(type:"string",length: 50,nullable: false)]
    private ?string $jour = null;

    #[ORM\Column(type:"string",length: 50,nullable: false)]
    private ?string $heure_ouverture = null;

    #[ORM\Column(type:"string",length: 50,nullable: false)]
    private ?string $heure_fermeture = null;
    public function getHoraireId(): ?int
    {
        return $this->horaire_id;
    }

    public function setHoraireId(int $horaire_id): static
    {
        $this->horaire_id = $horaire_id;

        return $this;
    }

    public function getJour(): ?string
    {
        return $this->jour;
    }   
    
    public function setJour(string $jour): static
    {
        $this->jour = $jour;

        return $this;
    }

    public function getHeureOuverture(): ?string
    {
        return $this->heure_ouverture;
    }

    public function setHeureOuverture(string $heure_ouverture): static
    {
        $this->heure_ouverture = $heure_ouverture;

        return $this;
    }

    public function getHeureFermeture(): ?string
    {
        return $this->heure_fermeture;
    }

    public function setHeureFermeture(string $heure_fermeture): static
    {
        $this->heure_fermeture = $heure_fermeture;

        return $this;
    }
}   