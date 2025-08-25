<?php

namespace App\Controller;

use App\Entity\Product;
use App\service\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CartController extends AbstractController
{
    public function __construct(private readonly ProductRepository $productRepository)
    {   // private :accessible que depuis l'inetance de la classe
        // readonly: accessible en lecture seule, ne peut pas être modifié après l'initialisation
        // Le constructeur est utilisé pour injecter le ProductRepository dans le contrôleur
        // Cela permet d'accéder aux méthodes de ProductRepository dans les actions du contrôleur

    }

    #[Route('/cart', name: 'app_cart', methods:['GET'])]
    public function index(SessionInterface $session, Cart $cart): Response
    {
        $data= $cart->getCart($session);

        return $this->render('cart/index.html.twig', [
            'items' => $data ['cart'],
            'total' =>$data ['total'],
        ]);

    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', methods:['GET'])]
    public function addProductToCart(int $id,SessionInterface $session, Product $product): Response
    {
        
        $cart =$session->get('cart', []); // Récupère les données du panier en session, ou un empty table
        
        // if(!isset($cart[$id]) && $product-> getStock() > 0 || $cart[$id] < $product-> getStock() ){
        //     if (!empty($cart[$id])){
        //         $cart[$id]++;
        //     }else{
        //         $cart[$id]=1; 
        //     } // si le produit est déja dans le panier, incremente sa quantité, sinon ajouté
        // }else{
        //     $this->addFlash('danger', 'Vous avez atteint la quantité maximale disponible pour ce produit.');
        //     return $this->redirectToRoute('app_home_page');
        // }
        if (!isset($cart[$id])) {
            if ($product->getStock() > 0) {
                $cart[$id] = 1; // Ajouter le produit au panier avec une quantité de 1
            } else {
                $this->addFlash('danger', 'Le produit est en rupture de stock.');
                return $this->redirectToRoute('app_home_page');
            }
        } elseif ($cart[$id] < $product->getStock()) {
            $cart[$id]++; // Incrémenter la quantité si elle est inférieure au stock disponible
        } else {
            $this->addFlash('danger', 'Vous avez atteint la quantité maximale disponible pour ce produit.');
            return $this->redirectToRoute('app_home_page');
        }

        $session->set('cart', $cart); // Met à jour le panier en session
        $this->addFlash('success', 'Le produit a été ajouté au panier avec succès.');
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
