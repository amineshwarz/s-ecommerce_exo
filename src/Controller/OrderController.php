<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request,ProductRepository $productRepository, SessionInterface $session ): Response
    {
        $cart = $session->get('cart', []);
        $cardWithDatas = [];

        foreach ($cart as $id => $quantity) {
            $cardWithDatas[] = [
                'products' => $productRepository->find($id),
                'quantity' => $quantity
            ];
        }

        $total = array_sum(array_map(function($item) {
            return $item['products']->getPrix() * $item['quantity'];
        }, $cardWithDatas));

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total' => $total,
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrix = $city->getShippingCost();

        return new Response(json_encode(['status' =>200, "message"=>'on', 'content' => $cityShippingPrix]));
    }
}


