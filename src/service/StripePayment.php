<?php 

namespace App\service;
use Stripe\Stripe;
use Stripe\Checkout\Session;



class StripePayment 
{
    private $redirectUrl;

    public function __construct()
    {
        Stripe::setApiKey($_SERVER['STRIPE_SECRET_KEY']);
        Stripe::setApiVersion('2024-06-20');
    }

    public function startPayment($cart, $shippingCost, $orderId){
        //dd($cart);
       // Récupération des produits du panier
       $cartProducts = $cart['cart']; 
       // Initialisation d'un tableau vide pour stocker les produits formatés
       $products = [ 
            [
                'qte' => 1,
                'prix' => $shippingCost,
                'name' => "Frais de livraison"
            ]
       ];

       // Boucle pour parcourir chaque produit du panier
       foreach ($cartProducts as $value) {
           // Initialisation d'un tableau vide pour stocker les informations d'un produit
           $productItem = [];
           // Récupération du nom du produit
           $productItem['name'] = $value['product']->getName();
           // Récupération du prix du produit
           $productItem['prix'] = $value['product']->getPrix();
           // Récupération de la quantité du produit
           $productItem['qte'] = $value['quantity'];
           // Ajout du produit formaté au tableau des produits
           $products[] = $productItem;
       }
        $session = Session::create([  // création de la session Strip
            'line_items'=>[  //produits qui vont etre payer
                array_map(fn(array $product) => [
                    'quantity' => $product['qte'],
                    'price_data' => [
                        'currency' => 'Eur',
                        'product_data' => [
                           'name' => $product['name']
                        ],
                        'unit_amount' => $product['prix']*100, //prix donnée en centimes donc on multiplie
                    ],
                ],$products)
            ],
            'mode'=>'payment', //mode de paiment
            'cancel_url' => 'http://localhost:8000/pay/cancel',
            'success_url' => 'http://localhost:8000/pay/success',
            'billing_address_collection' => 'required',
            'shipping_address_collection' => [
                'allowed_countries' => ['FR'],
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'orderId' =>$orderId//id de la commande
                ]
                ]

        ]);
        $this->redirectUrl = $session->url;
    }
    public function getStripeRedirectUrl(){ //permet de recuperer l'url de l'utilisateur pour stripe
        return $this->redirectUrl;
    }


} 
