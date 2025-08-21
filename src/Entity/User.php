<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)] //Indique que cette classe est une entité Doctrine. Reliée à la BDD via un repository (UserRepository).
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])] // Ajoute une contrainte d’unicité en base de données sur le champ email.
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')] // Vérifie aussi côté formulaire qu’un utilisateur n’existe pas déjà avec le même email (plus sympa pour l’utilisateur que d’attendre l’erreur SQL).
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null; // Clé primaire (id) auto-générée par Doctrine.

    #[ORM\Column(length: 180)] // Champ email, longueur max 180.
    private ?string $email = null; // C’est l’identifiant unique de connexion → sera utilisé par getUserIdentifier().

    /**
     * @var list<string> The user roles
     */
    #[ORM\Column]
    private array $roles = []; // Tableau des rôles de l’utilisateur (ROLE_USER, ROLE_ADMIN, etc.). Par défaut vide → mais getRoles() force toujours ROLE_USER.

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null; // Jamais le mot de passe en clair. C’est géré dans le contrôleur (grâce au UserPasswordHasherInterface).

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;  // Stockage du prénom et du nom. Chacun limité à 255 caractères.

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    public function getId(): ?int
    {
        return $this->id;
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
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array 
    {
        $roles = $this->roles;
        // Garantit que chaque utilisateur a au minimum ROLE_USER.
        $roles[] = 'ROLE_USER';

        return array_unique($roles); // Supprime les doublons avec array_unique.
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): static // Permet de définir des rôles spécifiques (admin, modérateur, etc.).
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

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Ensure the session doesn't contain actual password hashes by CRC32C-hashing them, as supported since Symfony 7.3.
     */
    public function __serialize(): array // Redéfinition de la sérialisation pour éviter que le mot de passe haché apparaisse dans la session en clair.
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', $this->password);

        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void
    {
        // @deprecated, to be removed when upgrading to Symfony 8
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;

        return $this;
    }
}

// En résumé
// Cette entité modélise ton utilisateur complet :
// - Identifiant unique : l’adresse email.
// - Authentification : mot de passe hashé + rôles.
// - Infos perso : prénom, nom.
// - Sécurité avancée : unicité, sérialisation sécurisée, support de l’API Users de Symfony.
// Cycle d’inscription :
// - Le FormType hydrate cette entité (excepté le mot de passe).
// - Le contrôleur récupère plainPassword, le hash et le met dans $user->password.
// - Doctrine persiste l’entité User.
//-  Symfony Security utilise cette entité pour l’authentification (via UserInterface).