<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping\PostLoad;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 * fields={"email"},
 * message= "L'email que vous avez indiqué est déjà utilisé !" )
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $nom;
    
    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $prenom;
    

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Email()
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $tel;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", minMessage="Votre mot de passe doit faire minimum 6 caractères")
     *  @Assert\EqualTo(propertyPath="Confir_pwd", message="Vous n'avez pas tapé le même mot de passe")
     */
    private $Password;

     /**
     * @Assert\EqualTo(propertyPath="Password", message="Vous n'avez pas tapé le même mot de passe")
     */
    public $Confir_pwd;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $picture;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $roles = [];

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $numCom;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCreate;

    /**
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $createur;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     */
    private $ipAdress;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateLastLogin;

    /**
     * @ORM\OneToMany(targetEntity=Credit::class, mappedBy="admin", orphanRemoval=true)
     */
    private $credits;

    /**
     * @ORM\OneToMany(targetEntity=Debit::class, mappedBy="admin", orphanRemoval=true)
     */
    private $debits;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isActif;

    /**
     * @ORM\OneToMany(targetEntity=Gestecommercial::class, mappedBy="admin", orphanRemoval=true)
     */
    private $gestecommercials;

    /**
     * @ORM\OneToMany(targetEntity=Seuil::class, mappedBy="admin", orphanRemoval=true)
     */
    private $seuils;

    /**
     * @ORM\OneToMany(targetEntity=SeuilBilleterie::class, mappedBy="users", orphanRemoval=true)
     */
    private $seuilBilleteries;

    /**
     * @ORM\OneToMany(targetEntity=Seuilpercepteur::class, mappedBy="admin")
     */
    private $seuilpercepteurs;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $session_token;

    public function __construct()
    {
        $this->dateCreate = new \DateTime();
        $this->credits = new ArrayCollection();
        $this->debits = new ArrayCollection();
        $this->gestecommercials = new ArrayCollection();
        $this->seuils = new ArrayCollection();
        $this->seuilBilleteries = new ArrayCollection();
        $this->seuilpercepteurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTel(): ?string
    {
        return $this->tel;
    }

    public function setTel(string $tel): self
    {
        $this->tel = $tel;

        return $this;
    }

    
    public function getPassword(): ?string
    {
        return $this->Password;
    }
    
    public function setPassword(string $Password): self
    {
        $this->Password = $Password;
        
        return $this;
    }
    
    public function getUsername(): ?string
    {
        return $this->email;
    }
    
    
    public function eraseCredentials()
    {
        
    }
    public function getSalt()
    {
        
    }
    
    public function setNumCom(?string $numCom): self
    {
        $this->numCom = $numCom;

        return $this;
    }
    
    public function getPicture(): ?string
    {
        return $this->picture;
    }
    
    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;
        
        return $this;
    }
    
    public function getRoles()
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }
    public function setRoles(?array $roles): self
    {
        $this->roles = $roles;
        
        return $this;
    }

    public function getNumCom(): ?string
    {
        return $this->numCom;
    }


    public function getDateCreate(): ?\DateTimeInterface
    {
        return $this->dateCreate;
    }

    public function setDateCreate(?\DateTimeInterface $dateCreate): self
    {
        $this->dateCreate = $dateCreate;

        return $this;
    }

    public function getCreateur(): ?string
    {
        return $this->createur;
    }

    public function setCreateur(?string $createur): self
    {
        $this->createur = $createur;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getIpAdress(): ?string
    {
        return $this->ipAdress;
    }

    public function setIpAdress(?string $ipAdress): self
    {
        $this->ipAdress = $ipAdress;

        return $this;
    }

    public function getDateLastLogin(): ?\DateTimeInterface
    {
        return $this->dateLastLogin;
    }

    public function setDateLastLogin(?\DateTimeInterface $dateLastLogin): self
    {
        $this->dateLastLogin = $dateLastLogin;

        return $this;
    }

    /**
     * @return Collection|Credit[]
     */
    public function getCredits(): Collection
    {
        return $this->credits;
    }

    public function addCredit(Credit $credit): self
    {
        if (!$this->credits->contains($credit)) {
            $this->credits[] = $credit;
            $credit->setAdmin($this);
        }

        return $this;
    }

    public function removeCredit(Credit $credit): self
    {
        if ($this->credits->removeElement($credit)) {
            // set the owning side to null (unless already changed)
            if ($credit->getAdmin() === $this) {
                $credit->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Debit[]
     */
    public function getDebits(): Collection
    {
        return $this->debits;
    }

    public function addDebit(Debit $debit): self
    {
        if (!$this->debits->contains($debit)) {
            $this->debits[] = $debit;
            $debit->setAdmin($this);
        }

        return $this;
    }

    public function removeDebit(Debit $debit): self
    {
        if ($this->debits->removeElement($debit)) {
            // set the owning side to null (unless already changed)
            if ($debit->getAdmin() === $this) {
                $debit->setAdmin(null);
            }
        }

        return $this;
    }

    public function getIsActif(): ?bool
    {
        return $this->isActif;
    }

    public function setIsActif(?bool $isActif): self
    {
        $this->isActif = $isActif;

        return $this;
    }

    /**
     * @return Collection|Gestecommercial[]
     */
    public function getGestecommercials(): Collection
    {
        return $this->gestecommercials;
    }

    public function addGestecommercial(Gestecommercial $gestecommercial): self
    {
        if (!$this->gestecommercials->contains($gestecommercial)) {
            $this->gestecommercials[] = $gestecommercial;
            $gestecommercial->setAdmin($this);
        }

        return $this;
    }

    public function removeGestecommercial(Gestecommercial $gestecommercial): self
    {
        if ($this->gestecommercials->removeElement($gestecommercial)) {
            // set the owning side to null (unless already changed)
            if ($gestecommercial->getAdmin() === $this) {
                $gestecommercial->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Seuil[]
     */
    public function getSeuils(): Collection
    {
        return $this->seuils;
    }

    public function addSeuil(Seuil $seuil): self
    {
        if (!$this->seuils->contains($seuil)) {
            $this->seuils[] = $seuil;
            $seuil->setAdmin($this);
        }

        return $this;
    }

    public function removeSeuil(Seuil $seuil): self
    {
        if ($this->seuils->removeElement($seuil)) {
            // set the owning side to null (unless already changed)
            if ($seuil->getAdmin() === $this) {
                $seuil->setAdmin(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|SeuilBilleterie[]
     */
    public function getSeuilBilleteries(): Collection
    {
        return $this->seuilBilleteries;
    }

    public function addSeuilBilletery(SeuilBilleterie $seuilBilletery): self
    {
        if (!$this->seuilBilleteries->contains($seuilBilletery)) {
            $this->seuilBilleteries[] = $seuilBilletery;
            $seuilBilletery->setUsers($this);
        }

        return $this;
    }

    public function removeSeuilBilletery(SeuilBilleterie $seuilBilletery): self
    {
        if ($this->seuilBilleteries->removeElement($seuilBilletery)) {
            // set the owning side to null (unless already changed)
            if ($seuilBilletery->getUsers() === $this) {
                $seuilBilletery->setUsers(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Seuilpercepteur[]
     */
    public function getSeuilpercepteurs(): Collection
    {
        return $this->seuilpercepteurs;
    }

    public function addSeuilpercepteur(Seuilpercepteur $seuilpercepteur): self
    {
        if (!$this->seuilpercepteurs->contains($seuilpercepteur)) {
            $this->seuilpercepteurs[] = $seuilpercepteur;
            $seuilpercepteur->setAdmin($this);
        }

        return $this;
    }

    public function removeSeuilpercepteur(Seuilpercepteur $seuilpercepteur): self
    {
        if ($this->seuilpercepteurs->removeElement($seuilpercepteur)) {
            // set the owning side to null (unless already changed)
            if ($seuilpercepteur->getAdmin() === $this) {
                $seuilpercepteur->setAdmin(null);
            }
        }

        return $this;
    }

    public function getSessionToken(): ?string
    {
        return $this->session_token;
    }

    public function setSessionToken(?string $session_token): self
    {
        $this->session_token = $session_token;

        return $this;
    }

}
