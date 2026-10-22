<?php

namespace App\Entity;

use App\Repository\ProposePlatRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProposePlatRepository::class)]
#[ORM\Table(name: '`proposeplat`')]     
#[UniqueEntity(fields: ['menu_id', 'plat_id'], message: 'Cette combinaison de menu et de plat existe déjà')]
class ProposePlat { 
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: "proposePlat")]
    #[ORM\JoinColumn(name: "menu_id", referencedColumnName: "menu_id", onDelete: "CASCADE")]
    private ?Menu $menu_id = null;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Plat::class, inversedBy: "proposePlat")]
    #[ORM\JoinColumn(name: "plat_id", referencedColumnName: "plat_id", onDelete: "CASCADE")]
    private ?Plat $plat_id = null;  

    public function getMenuId(): ?Menu
    {
        return $this->menu_id;
    }

    public function setMenuId(?Menu $menu_id): static
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    public function getPlatId(): ?Plat
    {
        return $this->plat_id;
    }

    public function setPlatId(?Plat $plat_id): static
    {
        $this->plat_id = $plat_id;

        return $this;
    }
}