<?php 
 
namespace App\service;
use App\Repository\ProductRepository;
 
class Cart {


    public function __construct(private readonly ProductRepository $productRepository)
    {   // private :accessible que depuis l'inetance de la classe
        // readonly: accessible en lecture seule, ne peut pas être modifié après l'initialisation
        // Le constructeur est utilisé pour injecter le ProductRepository dans le contrôleur
        // Cela permet d'accéder aux méthodes de ProductRepository dans les actions du contrôleur

    }
    public function getCart ($session):array {
        $cart =$session->get('cart', []); // Récupère les données du panier en session, ou un empty table
        $cartWithData=[]; //Initialisation table pour stocker les donné du panier 
        foreach ($cart as $id =>$quantity){ // boucle sur les element du panier pour recuperer les info de produit
            $cartWithData[] = [ // récupère le produit correspondant a l'id et la quantité
                'product'=> $this->productRepository->find($id), // récuperer le produit par son id 
                'quantity' => $quantity // quantité du produit dans le panier
            ];
        }

        $total = array_sum(array_map(function($item){ // calcul du totlal de panier
            return $item['product'] ->getprix() * $item['quantity']; // pour chque élément du panier, multiplie le prix de proquit par quantity
        }, $cartWithData));

        return[
            'cart' =>$cartWithData,
            'total'=> $total,
        ];
    }
}