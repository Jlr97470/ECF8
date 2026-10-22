<?php

namespace App\Entity;
class Retrieve
{
    public ?string $email = null;

    public function getEmail(): ?string
    {
        return $this->email;
    }

}