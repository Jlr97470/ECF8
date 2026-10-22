<?php

namespace App\Entity;

use App\Repository\ProposeThemeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: ProposeThemeRepository::class)]
#[ORM\Table(name: '`proposetheme`')]     
#[UniqueEntity(fields: ['menu_id'], message: 'Ce menu est déjà associé à un thème.')]
class ProposeTheme { 

    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Menu::class)]
    #[ORM\JoinColumn(name: "menu_id", referencedColumnName: "menu_id", onDelete: "CASCADE")]
    private ?Menu $menu_id = null;

    #[ORM\ManyToOne(targetEntity: Theme::class, inversedBy: "proposeThemes")]
    #[ORM\JoinColumn(name: "theme_id", referencedColumnName: "theme_id", onDelete: "CASCADE")]
    private ?Theme $theme_id = null;  

    public function getMenuId(): ?Menu
    {
        return $this->menu_id;
    }

    public function setMenuId(?Menu $menu_id): static
    {
        $this->menu_id = $menu_id;

        return $this;
    }

    public function getThemeId(): ?Theme    
    {
        return $this->theme_id;
    }

    public function setThemeId(?Theme $theme_id): static
    {
        $this->theme_id = $theme_id;

        return $this;
    }
}