<?php

namespace App\Controller;

use App\Entity\Product;
use App\service\Cart;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

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
    public function addProductToCart(int $id,SessionInterface $session, Product $product, Request $request): Response
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

        // ------------------------ Redirection a condition de l'url precedente ------------------------ 
        
        // Récupérer l’URL précédente (referer)
        $referer = $request->headers->get('referer'); // Cette URL sert à savoir d’où vient la requête (par exemple, page panier ou page d’accueil).

        if ($referer) { // Vérifie si une URL référente a bien été trouvée (elle peut être absente selon le navigateur ou contexte).
            // Exemple simplifié: si l’URL contient "/cart" on reste dans le panier
            if (str_contains($referer, '/cart')) { // La fonction str_contains retourne vrai si cette sous-chaîne est présente dans l’URL référente.
                return $this->redirectToRoute('app_cart'); // Cela permet de rester sur la page panier après l’opération.
            }
        }
        // Par défaut, redirige vers la page d’accueil
        return $this->redirectToRoute('app_home_page');
        
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

    #[Route('/cart/decrease/{id}', name: 'app_cart_decrease', methods:['GET'])]
    public function decreaseProductQuantity(int $id, SessionInterface $session): Response
    {
        $cart = $session->get('cart', []); //Récupère le contenu actuel du panier depuis la session sous la clé 'cart'.

        if (isset($cart[$id])) { // Vérifie si le produit avec l'ID fourni existe déjà dans le panier.
            if ($cart[$id] > 1) { // Vérifie si la quantité de ce produit dans le panier est supérieure à 1.
                $cart[$id]--; // Diminue la quantité du produit dans le panier de 1 unité.
            } else { // Si la quantité était égale à 1, alors la décrémenter reviendrait à 0, donc on choisit de supprimer complètement ce produit du panier avec unset.
                unset($cart[$id]); 
            }
            $session->set('cart', $cart); // Met à jour le panier dans la session utilisateur avec la nouvelle version modifiée (quantité diminuée ou produit supprimé).
            $this->addFlash('success', 'La quantité du produit a été diminuée.');
        } else {
            $this->addFlash('danger', 'Ce produit n\'est pas dans le panier.');
        }

        return $this->redirectToRoute('app_cart');
    }

}
 // creation d'une route qui permet de retourner sur le panier quans on ajoute un article demander un quantin si on est obliger ou bien dans la route en haut en peut faire une condition 