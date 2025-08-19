<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Order;
use App\service\Cart;
use DateTimeImmutable;
use App\Form\OrderType;
use App\Entity\OrderProducts;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Mime\Email;
use App\Repository\OrderRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;

final class OrderController extends AbstractController
{
    public function __construct(private MailerInterface $mailer)
    {
    }

    #[Route('/order', name: 'app_order')]
    public function index(Request $request, SessionInterface $session, EntityManagerInterface $em, Cart $cart): Response
    {
        $data = $cart->getCart($session);

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if($order->isPayOnDelivery()){
                if(!empty($data['total'])){

                    $shippingCost = $order->getCity()->getShippingCost(); // pour ajouter les frais de livraison a vec le prix de la commande
                    $totalWithShipping = $data['total'] + $shippingCost;

                    $order->setTotalPrice($totalWithShipping);
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

            $html = $this->renderView('order/orderMessage.html.twig', [ // crée une vue mail
                'order' => $order, // on recupere le order apres flush donc on a toutes les info 
            ]);
            $email =(new Email()) // on importe la classe depuis Symfony\Component\Mime\Email;
            -> from ('shop@gmailcom') // l'adresse de l'expéditeur notre adresse email
            -> to('elkhal.medamine@gmail.com') // l'adresse du destinataire
            -> subject('Confirmation de reception de commande') // Intitulé du mail
            -> html($html); // le contenu du mail
            $this->mailer->send($email); // envoi du mail
            $this->addFlash('success', 'Votre commande a été enregistrée avec succès. Un email de confirmation vous a été envoyé.');
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
    #[Route('editor/order/{id}/is-completed/update', name: 'app_order_is-completed-update')]
    public function isCompletedUpdate($id, OrderRepository $orderRepository, EntityManagerInterface $em,): Response
    {
        $order = $orderRepository->find($id);

        $order->setIsCompleted(true);
        $em->persist($order);
        $em->flush();
        $this->addFlash('success', 'modification effectuée avec succès');
        return $this->redirectToRoute('app_orders_show');
    }

    #[Route('/editor/order/{id}/remove', name:"app_order_remove")]
    public function removeOrder (Order $order, EntityManagerInterface $em): Response
    {
        $em->remove($order);
        // dd($order);
        $em->flush();
        $this->addFlash('success', 'La commande a été supprimée avec succès');
        return $this->redirectToRoute('app_orders_show');
    }
}


