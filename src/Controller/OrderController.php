<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\service\Cart;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
                if(!empty($data['total'])){
                    $order -> setTotalPrice($data['total']);
                    $order -> setCreatedAt(new DateTimeImmutable());
                    // $order-> setCreatedAt( new \DataTimeImmutable());
                    $em -> persist($order);
                    $em -> flush();

                    foreach($data['cart'] as $value) {
                        $orderProduct = new OrderProducts();
                        $orderProduct->setOrder($order);
                        $orderProduct->setProduct($value['product']);
                        $orderProduct->setQte($value['quantity']);
                        $em->persist($orderProduct);
                        $em->flush();
                    }
                }
            }
             $session->set('cart', []); // Mise ajour du contenu du panier en session
             return $this->redirectToRoute('order_message'); // Redirection vers la page du panier

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

    #[Route('/order_message', name: 'order_message')]
    public function orderMessage(): Response
    {

        return $this->render('order/orderMessage.html.twig');
    }

    
    #[Route('/editor/order', name: 'app_orders_show')]
    public function getAllOrder(OrderRepository $orderRepository, PaginatorInterface $paginator, Request $request, ProductRepository $productRepository): Response
    {
  

        $data = $orderRepository->findBy([], ['id' => 'DESC']);
        $orders = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            6
        );
        return $this->render('order/orders.html.twig',[
            'orders' => $orders,
 
        ]);
    }
}


