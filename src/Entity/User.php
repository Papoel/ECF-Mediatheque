<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Scheb\TwoFactorBundle\Model\Email\TwoFactorInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity(
 *     fields={"email"},
 *     message="Il existe déjà un compte avec cette adresse e-mail,"
 * )
 * @ORM\Table(name="users")
 */
class User implements UserInterface,
    PasswordAuthenticatedUserInterface,
    TwoFactorInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $prenom;

    /**
     * @ORM\Column(type="date")
     */
    private $date_naissance;

    /**
     * @ORM\Column(type="text")
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(
     * message = "Cet email '{{ value }}' n'est pas une adresse email valide."
     * )
     */
    private $email;

    /**
     * @var                       string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min = 6,
     *      max = 50,
     *      minMessage = "Votre mot de passe doit comporter au moins {{ limite }} caractères.",
     *      maxMessage = "Votre mot de passe ne peut pas comporter plus de {{ limite }} caractères."
     * )
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isAutorise;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $authCode;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\Positive
     */
    private $emprunt_max;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="user")
     */
    private $reservations;

    public function __construct()
    {
        $this->isAutorise = false;
        $this->roles = ['ROLE_USER'];
        $this->emprunt_max = 5;
        $this->reservations = new ArrayCollection();
    }
    /**
     * Retourne le nom compet d'un utilisateur
     *
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getPrenom().' '.$this->getNom();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function isEmailAuthEnabled(): bool
    {
        return true;
    }

    public function getEmailAuthRecipient(): string
    {
        return $this->email;
    }

    public function getEmailAuthCode(): string
    {
        if (null === $this->authCode) {
            throw new \LogicException("L'email de vérification n'a pas été envoyé.");
        }

        return $this->authCode;
    }

    public function setEmailAuthCode(string $authCode): void
    {
        $this->authCode = $authCode;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->date_naissance;
    }

    public function setDateNaissance(\DateTimeInterface $date_naissance): self
    {
        $this->date_naissance = $date_naissance;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getIsAutorise(): ?bool
    {
        return $this->isAutorise;
    }

    public function setIsAutorise(bool $isAutorise): self
    {
        $this->isAutorise = $isAutorise;

        return $this;
    }

    public function getEmpruntMax(): ?int
    {
        return $this->emprunt_max;
    }

    public function setEmpruntMax(?int $emprunt_max): self
    {
        $this->emprunt_max = $emprunt_max;

        return $this;
    }

    public function __toString()
    {
        return $this->prenom .' '. $this->nom;
    }

    public function deduitUnEmpruntMax(): self
    {
        if ($this->emprunt_max > 0) {
            $this->emprunt_max = $this->emprunt_max - 1;
        }

        return $this;
    }

    public function ajouterUnEmpruntMax(): self
    {
        $this->emprunt_max = $this->emprunt_max + 1;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->setUser($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getUser() === $this) {
                $reservation->setUser(null);
            }
        }

        return $this;
    }
}
