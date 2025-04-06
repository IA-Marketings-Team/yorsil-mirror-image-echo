<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints as Assert;


class PasswordUpdate
{
    

    private $oldPassword;

   /**
    *  @Assert\Length(min="6", minMessage="Votre mot de passe doit faire minimum 6 caractères")
    * @Assert\EqualTo(propertyPath="comfirmPassword", message="Vous n'avez pas tapé le même mot de passe")
    */
    private $newPassword;

    /**
     * @Assert\EqualTo(propertyPath="newPassword", message="Vous n'avez pas tapé le même mot de passe")
     */
    private $comfirmPassword;


    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getComfirmPassword(): ?string
    {
        return $this->comfirmPassword;
    }

    public function setComfirmPassword(string $comfirmPassword): self
    {
        $this->comfirmPassword = $comfirmPassword;

        return $this;
    }
}
