<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use App\Repository\MenuRepository;

#[ORM\Entity(repositoryClass: MenuRepository::class)]
#[ORM\Table(name: '`menu`')]
#[UniqueEntity(fields: ['titre'], message: 'Le titre existe déjà')]
class Menu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $menu_id = null;

    #[ORM\Column(length: 50, unique: true, nullable: false)]
    private ?string $titre = null;

    #[ORM\Column(type:"integer", nullable: false)]
    private ?int $nombre_personne_minimum = null;

    #[ORM\Column(type:"float", nullable: false)]
    private ?float $prix_par_personne = null;

    #[ORM\Column(length: 50)]
    private ?string $regime = null;

    #[ORM\Column(length: 50, nullable: false)]
    private ?string $description = null;

    #[ORM\Column(type:"integer", nullable: false)]
    private ?int $quantite_restante = null;

    #[ORM\OneToMany(targetEntity: ProposePlat::class, mappedBy: 'menu_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $proposeplat;

    #[ORM\OneToOne(targetEntity: ProposeTheme::class, mappedBy: 'menu_id', orphanRemoval: true, cascade: ['persist'])]
    private ?ProposeTheme $proposetheme = null;

    #[ORM\OneToOne(targetEntity: Adapte::class, mappedBy: 'menu_id', orphanRemoval: true, cascade: ['persist'])]
    private ?Adapte $adapte = null;    
    public function __construct()
    {
        $this->proposeplat = new ArrayCollection();
    }
    public function getMenuId(): ?int
    {
        return $this->menu_id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getNombrePersonneMinimum(): ?int
    {
        return $this->nombre_personne_minimum;
    }

    public function setNombrePersonneMinimum(int $nombre_personne_minimum): self
    {
        $this->nombre_personne_minimum = $nombre_personne_minimum;

        return $this;
    }

    public function getPrixParPersonne(): ?float
    {
        return $this->prix_par_personne;
    }

    public function setPrixParPersonne(float $prix_par_personne): self
    {
        $this->prix_par_personne = $prix_par_personne;

        return $this;
    }

    public function getRegime(): ?string
    {
        return $this->regime;
    }

    public function setRegime(string $regime): self
    {
        $this->regime = $regime;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getQuantiteRestante(): ?int
    {
        return $this->quantite_restante;
    }

    public function setQuantiteRestante(int $quantite_restante): self
    {
        $this->quantite_restante = $quantite_restante;

        return $this;
    }
    /**
    * @return Collection<int, ProposePlat>
    */
    public function getProposeplats(): Collection
    {       
        return $this->proposeplat;
    }
    public function addProposeplat(ProposePlat $proposeplat): static
    {
        if (!$this->proposeplat->contains($proposeplat)) {
            $this->proposeplat->add($proposeplat);
            $proposeplat->setMenuId($this);
        }

        return $this;
    }   

    public function removeProposeplat(ProposePlat $proposeplat): static
    {
        if ($this->proposeplat->removeElement($proposeplat)) {
            // set the owning side to null (unless already changed)
            if ($proposeplat->getMenuId() === $this) {
                $proposeplat->setMenuId(null);
            }
        }

        return $this;
    }
    /**
     * @return ProposeTheme<int, ProposeTheme>
     */
    public function getProposeTheme(): ?ProposeTheme
    {
        return $this->proposetheme;
    }

    public function addProposeTheme(ProposeTheme $proposetheme): static
    {
        if ($this->proposetheme !== $proposetheme) {
            $this->proposetheme = $proposetheme;
            $proposetheme->setMenuId($this);
        }

        return $this;    
    }

    public function removeProposeTheme(ProposeTheme $proposetheme): static
    {
        if ($this->proposetheme === $proposetheme) {
            $this->proposetheme = null;
        }

        return $this;
    }
    /**
     * @return Adapte<int, Adapte>
     */
    public function getAdapte(): ?Adapte
    {
        return $this->adapte;
    }

    public function addAdapte(Adapte $adapte): static
    {
        if ($this->adapte !== $adapte) {
            $this->adapte = $adapte;
            $adapte->setMenuId($this);
        }

        return $this;    
    }

    public function removeAdapte(Adapte $adapte): static
    {
        if ($this->adapte === $adapte) {
            $this->adapte = null;
        }

        return $this;
    }

}