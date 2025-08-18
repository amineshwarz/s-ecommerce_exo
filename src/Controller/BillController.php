<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Dompdf\Options;
use App\Repository\OrderRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;



#[IsGranted('ROLE_EDITOR')]
final class BillController extends AbstractController
{
   
    #[Route('/editor/order/{id}/bill', name: 'app_bill')]
    public function index($id, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->find($id);
        
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $domPdf = new Dompdf($pdfOptions);
        $html = $this->renderView('bill/index.html.twig', [
            'order' => $order,
        ]);
        $domPdf->loadHtml($html);
        $domPdf->render();
        $domPdf-> stream ('bill-' . $order->getId() . '.pdf', [
            'Attachment' => true
        ]);

        return new Response('', 200, [
            'Content-Type' => 'application/pdf',
        ]);






        return $this->render('bill/index.html.twig', [
            'order' => $order,
        ]);
    }
}
