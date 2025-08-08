<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\service\Cart;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


final class OrderController extends AbstractController
{
    #[Route('/order', name: 'app_order')]
    public function index(Request $request,ProductRepository $productRepository, SessionInterface $session, EntityManagerInterface $em, Cart $cart): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if($order->isPayOnDelivery()){
                $order -> setTotalPrice($data['total']);
                $order -> setCreatedAt(new DateTimeImmutable());
                // $order-> setCreatedAt( new \DataTimeImmutable());
                $em -> persist($order);
                $em -> flush();
            }
        }


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'total' => $data['total'],
        ]);
    }

    #[Route('/city/{id}/shipping/cost', name: 'app_city_shipping_cost')]
    public function cityShippingCost(City $city): Response
    {
        $cityShippingPrix = $city->getShippingCost();

        return new Response(json_encode(['status' =>200, "message"=>'on', 'content' => $cityShippingPrix]));
    }
}


