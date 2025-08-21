<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //    /**
    //     * @return Product[] Returns an array of Product objects
    //     */
    //    public function findByIdUp($value): array
    //    {
    //        return $this->createQueryBuilder('p') //retourne la requete
    //            ->andWhere('p.id > :val') // ajoute des critères val = $value
    //            ->setParameter('val', $value) // on set les parametres
    //            ->orderBy('p.id', 'ASC') // on definit les criteres
    //            ->setMaxResults(10) //definit le nbr de resultat
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Product
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function searchEngine (string $query) 
       {
           return $this->createQueryBuilder('p') // Crée un objet de requête qui permet de construire la requête recherche
               ->Where('p.Name LIKE :query') // Recherche les éléménts dont le nom contient la reqête de recherche 
               ->orWhere('p.description LIKE :query') // ou rechercher les élées dont la desciption contient la requete de recherche 
               ->setParameter('query', '%' . $query . '%') // Défini la valeur de la variable "query" pour la requete
               ->getQuery() // Éxecute la reqête et récupère les résultats
               ->getResult()
           ;
       }
}
