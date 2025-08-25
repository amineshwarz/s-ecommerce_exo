<?php

namespace App\Entity;

use App\Repository\CategorieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategorieRepository::class)]
class Categorie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;   // Clé primaire de la table, générée automatiquement.

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, SubCategory>
     */
    #[ORM\OneToMany(targetEntity: SubCategory::class, mappedBy: 'category', orphanRemoval: true)] 
    //Relation un-à-plusieurs : une catégorie contient plusieurs <SubCategory>
    // mappedBy: 'category' → La propriété dans SubCategory qui référence la catégorie parente.
    //orphanRemoval: true → Si la sous-catégorie est retirée de la collection, elle est aussi supprimée de la base (gère l’intégrité des données).
    private Collection $subCategories;

    public function __construct() //Initialise la collection de sous-catégories dès la création de l’objet Catégorie, Évite les erreurs sur les collections nulles.
    {
        $this->subCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, SubCategory>
     */
    public function getSubCategories(): Collection
    {
        return $this->subCategories;
    }

    public function addSubCategory(SubCategory $subCategory): static
    {
        if (!$this->subCategories->contains($subCategory)) { // Évite d’ajouter deux fois la même sous-catégorie.
            $this->subCategories->add($subCategory); // En plus d’ajouter à la collection, on met à jour la sous-catégorie pour qu’elle pointe sur cette catégorie (synchronise les deux côtés de la relation).
            $subCategory->setCategory($this);
        }

        return $this;
    }

    public function removeSubCategory(SubCategory $subCategory): static
    {
        if ($this->subCategories->removeElement($subCategory)) {
            // set the owning side to null (unless already changed)
            if ($subCategory->getCategory() === $this) {
                $subCategory->setCategory(null);
            }
        }

        return $this;
    }
    public function __toString()
    { // Permet d’afficher une catégorie par son nom dans les templates ou l’admin (ex : EasyAdmin).
    return $this->getName(); 
    }
}

// Résumé
// Cette entité :
    // Modélise une catégorie avec un nom et plusieurs sous-catégories associées.
    // Gère automatiquement l’ajout, le retrait, et la suppression efficace des sous-catégories.    
    // Facilite l’usage dans Symfony (templates, admin, formulaires) grâce à __toString().