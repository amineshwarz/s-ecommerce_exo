<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {   // private :accessible que depuis l'inetance de la classe
        // readonly: accessible en lecture seule, ne peut pas être modifié après l'initialisation
        // Le constructeur est utilisé pour injecter le ProductRepository dans le contrôleur
        // Cela permet d'accéder aux méthodes de ProductRepository dans les actions du contrôleur

    }

    #[Route('/cart', name: 'app_cart', methods:['GET'])]
    public function index(SessionInterface $session): Response
    {
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

        return $this->render('cart/index.html.twig', [
            'items' =>$cartWithData,
            'total'=> $total,
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods:['GET'])]
    public function addProductToCart(int $id,SessionInterface $session): Response
    {
        $cart =$session->get('cart', []); // Récupère les données du panier en session, ou un empty table
        if (!empty($cart[$id])){
            $cart[$id]++;
        }else{
            $cart[$id]=1; 
        } // si le produit est déja dans le panier, incremente sa quantité, sinon ajouté
        $session->set('cart', $cart); // met a jour le panier dans la session

        return $this->redirectToRoute('app_cart');
    }

    #[Route('cart/delete/{id}', name: 'app_cart_delete', methods: ['GET'])]
    public function removetToCart(int $id, SessionInterface $session): Response
    { 
        // Récupère le contenu du panier en session, ou initialise un tableau vide si le panier n'existe pas
        $cart = $session->get('cart', []);
        // Vérifie si le produit à supprimer est dans le panier
        if (!empty($cart[$id])) {
            // suppression du produit du panier
            unset($cart[$id]);
        }
        // Met à jour le panier dans la session
        $session->set('cart', $cart);
        // Redirige vers la page du panier après l'ajout du produit
        return $this->redirectToRoute('app_cart');
    }

    #[Route('cart/remove', name: 'app_cart_remove', methods: ['GET'])]
    public function removeProductToCart(SessionInterface $session): Response
    { 
        // mise à jour du contenu du panier en session
        $session->set('cart', []);
        // Redirige vers la page du panier après la suppression de tous les produits
        return $this->redirectToRoute('app_cart');
    }


}
