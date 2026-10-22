<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use App\Repository\ThemeRepository;

#[ORM\Entity(repositoryClass: ThemeRepository::class)]
#[ORM\Table(name: '`theme`')]    
class Theme {
    #[ORM\Id]  
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $theme_id = null;

    #[ORM\Column(type:"string",length: 50)]
    private ?string $libelle = null;

    #[ORM\OneToMany(targetEntity: ProposeTheme::class, mappedBy: 'theme_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $proposetheme;
    
    public function __construct()
    {
        $this->proposetheme = new ArrayCollection();
    }

    public function getThemeId(): ?int
    {
        return $this->theme_id;
    }

    public function setThemeId(int $theme_id): static
    {
        $this->theme_id = $theme_id;

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
     * @return Collection<int, ProposeTheme>
     */
    public function getProposeThemes(): Collection
    {
        return $this->proposetheme;
    }

    public function addProposeTheme(ProposeTheme $proposetheme): static
    {
        if (!$this->proposetheme->contains($proposetheme)) {
            $this->proposetheme->add($proposetheme);
            $proposetheme->setThemeId($this);
        }

        return $this;
    }

    public function removeProposeTheme(ProposeTheme $proposetheme): static
    {
        if ($this->proposetheme->removeElement($proposetheme)) {
            // set the owning side to null (unless already changed)
            if ($proposetheme->getThemeId() === $this) {
                $proposetheme->setThemeId(null);
            }
        }

        return $this;
    }

}   