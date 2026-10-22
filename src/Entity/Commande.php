<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
#[ORM\Table(name: '`commande`')]
#[UniqueEntity(fields: ['numero_commande'], message: 'Le numéro de commande existe déjà')]
class Commande
{
    #[ORM\Id]
    #[ORM\Column(type:"string",length: 50, unique: true, nullable: false)]
    private ?string $numero_commande = null;

    #[ORM\Column(type:"date", nullable: false)]
    private ?\DateTime $date_commande = null;

    #[ORM\Column(type:"date", nullable: true)]
    private ?\DateTime $date_prestation = null;

    #[ORM\ManyToOne(targetEntity: Utilisateur::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: "utilisateur_id", referencedColumnName: "utilisateur_id", onDelete: "CASCADE")]
    private ?Utilisateur $utilisateur_id = null;

    #[ORM\ManyToOne(targetEntity: Menu::class, inversedBy: "commandes")]
    #[ORM\JoinColumn(name: "menu_id", referencedColumnName: "menu_id", onDelete: "CASCADE")]
    private ?Menu $menu_id = null;

    #[ORM\Column(type:"string",length: 50,nullable: false)]
    private ?string $heure_livraison = null;

    #[ORM\Column(type:"float",nullable: false)]
    private ?float $prix_menu = null;

    #[ORM\Column(type:"integer", nullable: false)]
    private ?int $nombre_personne = null;

    #[ORM\Column(type:"float", nullable: false)]
    private ?float $prix_livraison = null;

    #[ORM\Column(type:"string",length: 50,  nullable: false)]
    private ?string $statut = null; 

    #[ORM\Column(type:"boolean", nullable: false)]
    private ?bool $pret_materiel = null;

    #[ORM\Column(type:"boolean",nullable: false)]
    private ?bool $restitution_materiel = null;

    public function getNumeroCommande(): ?string
    {
        return $this->numero_commande;
    }   

    public function setNumeroCommande(string $numero_commande): static
    {
        $this->numero_commande = $numero_commande;

        return $this;
    }

    public function getDateCommande(): ?\DateTime
    {
        return $this->date_commande;
    }

    public function setDateCommande(\DateTime $date_commande): static
    {
        $this->date_commande = $date_commande;

        return $this;
    }

    public function getDatePrestation(): ?\DateTime
    {
        return $this->date_prestation;
    }

    public function setDatePrestation(?\DateTime $date_prestation): static
    {
        $this->date_prestation = $date_prestation;

        return $this;
    }

    public function getUtilisateurId(): ?Utilisateur
    {
        return $this->utilisateur_id;
    }

    public function setUtilisateurId(?Utilisateur $utilisateur_id): static
    {
        $this->utilisateur_id = $utilisateur_id;

        return $this;
    }

    public function getMenuId(): ?Menu
    {
        return $this->menu_id;
    }
    
    public function setMenuId(?Menu $menu_id): static
    {
        $this->menu_id = $menu_id;

        return $this;
    }
    
    public function getHeureLivraison(): ?string
    {
        return $this->heure_livraison;
    }

    public function setHeureLivraison(string $heure_livraison): static
    {
        $this->heure_livraison = $heure_livraison;

        return $this;
    }

    public function getPrixMenu(): ?float
    {
        return $this->prix_menu;
    }

    public function setPrixMenu(float $prix_menu): static
    {
        $this->prix_menu = $prix_menu;

        return $this;
    }

    public function getNombrePersonne(): ?float
    {
        return $this->nombre_personne;
    }
    
    public function setNombrePersonne(float $nombre_personne): static
    {
        $this->nombre_personne = $nombre_personne;

        return $this;
    }

    public function getPrixLivraison(): ?float
    {
        return $this->prix_livraison;
    }

    public function setPrixLivraison(float $prix_livraison): static
    {
        $this->prix_livraison = $prix_livraison;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function isPretMateriel(): ?bool
    {
        return $this->pret_materiel;
    }

    public function setPretMateriel(bool $pret_materiel): static
    {
        $this->pret_materiel = $pret_materiel;

        return $this;
    }

    public function isRestitutionMateriel(): ?bool
    {
        return $this->restitution_materiel;
    }

    public function setRestitutionMateriel(bool $restitution_materiel): static
    {
        $this->restitution_materiel = $restitution_materiel;

        return $this;
    }

}    