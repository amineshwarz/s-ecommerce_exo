<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('home_page/produit.html.twig', [
            'product' => $product,
        ]);
    }
}
