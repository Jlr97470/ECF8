<?php

namespace App\Entity;

use App\Repository\AdapteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: AdapteRepository::class)]
#[ORM\Table(name: '`adapte`')]     
#[UniqueEntity(fields: ['menu_id'], message: 'Ce menu est déjà associé à un régime.')]
class Adapte { 
    #[ORM\Id]    
    #[ORM\OneToOne(targetEntity: Menu::class, inversedBy: 'adapte')]
    #[ORM\JoinColumn(name: "menu_id", referencedColumnName: "menu_id", onDelete: "CASCADE")]
    private ?Menu $menu_id = null;
    
    #[ORM\ManyToOne(targetEntity: Regime::class, inversedBy: "adaptes")]
    #[ORM\JoinColumn(name: "regime_id", referencedColumnName: "regime_id", onDelete: "CASCADE")]
    private ?Regime $regime_id = null;  

    public function getMenuId(): ?Menu
    {
        return $this->menu_id;
    }

    public function setMenuId(?Menu $menu_id): static
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    public function getRegimeId(): ?Regime    
    {
        return $this->regime_id;
    }

    public function setRegimeId(?Regime $regime_id): static
    {
        $this->regime_id = $regime_id;

        return $this;
    }
}