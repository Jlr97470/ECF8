<?php

namespace App\Entity;
class Contact
{
    public ?string $email = null;
    public ?string $nom = null;
    public ?string $prenom = null;
    public ?string $titre = null;
    public ?string $message = null;
 
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }
    public function getNom(): ?string
    {
        return $this->nom;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }
    public function getTitre(): ?string
    {
        return $this->titre;
    }
    public function getMessage(): ?string
    {
        return $this->message;
    }
}