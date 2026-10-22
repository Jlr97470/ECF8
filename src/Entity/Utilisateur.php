<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

use App\Repository\UtilisateurRepository;


#[ORM\Entity(repositoryClass: UtilisateurRepository::class)]
#[ORM\Table(name: '`utilisateur`')]
#[UniqueEntity(fields: ['email'], message: 'Cette adresse email est déjà utilisée.')]
class Utilisateur implements UserInterface,PasswordAuthenticatedUserInterface
{

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $utilisateur_id = null;

    #[ORM\Column(length: 50, unique: true, nullable: false)]    
    private ?string $email;
     /**
     * @var string The hashed password
     */
    #[ORM\Column(type:"string", nullable: false)]
    private ?string $password = null;

    #[ORM\Column(type:"string",length:50, nullable: false)]
    private ?string $prenom = null;    

    #[ORM\Column(type:"string",length:50, nullable: false)]
    private ?string $nom = null;        

    #[ORM\Column(type:"string", length:50, nullable: true)]
    private ?string $telephone = null; 

    #[ORM\Column(type:"string", length:50, nullable: true)]
    private ?string $ville = null;       

    #[ORM\Column(type:"string", length:50, nullable: true)]
    private ?string $pays = null; 

    #[ORM\Column(type:"string",length:50, nullable: true)]
    private ?string $adresse_postal = null;  
    
    #[ORM\OneToOne(targetEntity: Possede::class, mappedBy: 'utilisateur_id', orphanRemoval: true, cascade: ['persist'])]
    private ?Possede $possede= null;

    #[ORM\OneToOne(targetEntity: Publie::class, mappedBy: 'utilisateur_id', orphanRemoval: true, cascade: ['persist'])]
    private ?Publie $publie= null;  
    
    #[ORM\OneToMany(targetEntity: Commande::class, mappedBy: 'utilisateur_id', orphanRemoval: true, cascade: ['persist'])]
    private Collection $commande; 
    private ?array $roles = null;
    public function getUtilisateurId(): ?int
    {
        return $this->utilisateur_id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): static
    {
        $this->password = $password;

        return $this;
    }
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    
    public function setPrenom(string $prenom): static
    {
        $this->prenom = $prenom;

        return $this;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    
    public function setNom(string $nom): static
    {
        $this->nom = $nom;

        return $this;
    }
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }   
    public function setTelephone(?string $telephone): static
    {
        $this->telephone = $telephone;

        return $this;
    }
    public function getVille(): ?string
    {
        return $this->ville;
    }   
    public function setVille(?string $ville): static
    {
        $this->ville = $ville;

        return $this;
    }
    public function getPays(): ?string
    {
        return $this->pays;
    }   
    public function setPays(?string $pays): static
    {
        $this->pays = $pays;

        return $this;
    }
    public function getAdressePostal(): ?string
    {
        return $this->adresse_postal;
    }
    public function setAdressePostal(?string $adresse_postal): static
    {
        $this->adresse_postal = $adresse_postal;

        return $this;
    }   
    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function setUsername(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $this->roles = [];
        
        if($this->possede && $this->possede->getRoleId()) {
            $this->roles[] = $this->possede->getRoleId()->getLibelle();
        }

        return $this->roles; 
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }
     /**
     * @return Possede<int, Possede>
     */
    public function getPossede(): Possede
    {
        if (!$this->possede) {
            $this->possede = new Possede();
            $this->possede->setUtilisateurId($this);
        }
        
        return $this->possede;
    }

    public function addPossede(Possede $possede): static
    {
        if ($this->possede !== $possede) {
            $this->possede = $possede;
            $possede->setUtilisateurId($this);
        }

        return $this;        
    }

    public function removePossede(Possede $possede): static
    {
        if ($this->possede === $possede) {
            $this->possede = null;
            // set the owning side to null (unless already changed)
            if ($possede->getUtilisateurId() === $this) {
                $possede->setUtilisateurId(null);
            }
        }       

        return $this;
    }
    /**
    * @return Publie<int, Publie>
    */  
    public function getPublie(): ?Publie
    {
        return $this->publie;
    }
    
    public function addPublie(Publie $publie): static
    {
        if ($this->publie !== $publie) {
            $this->publie = $publie;
            $publie->setUtilisateurId($this);
        }

        return $this;
    }

    public function removePublie(Publie $publie): static
    {
        if ($this->publie === $publie) {
            $this->publie = null;
            // set the owning side to null (unless already changed)
            if ($publie->getUtilisateurId() === $this) {
                $publie->setUtilisateurId(null);
            }
        }

        return $this;
    }

   public function getCommandes(): Collection
    {
        return $this->commande;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commande->contains($commande)) {
            $this->commande->add($commande);
            $commande->setUtilisateurId($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commande->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUtilisateurId() === $this) {
                $commande->setUtilisateurId(null);
            }
        }

        return $this;
    }  
    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }
    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
