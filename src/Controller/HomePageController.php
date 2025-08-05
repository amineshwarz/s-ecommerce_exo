<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Repository\CategorieRepository;
use App\Repository\SubCategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class HomePageController extends AbstractController
{
    #[Route('/', name: 'app_home_page', methods: ['GET'])]
    public function index(ProductRepository $productRepository, CategorieRepository $categorieRepository, Request $request, PaginatorInterface $paginator): Response
    {
        $products = $productRepository->findAll();
        $data = $productRepository->findBy([], ['id' => 'DESC']);
        $products = $paginator->paginate(
            $data,
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('home_page/index.html.twig', [
            'controller_name' => 'HomePageController',
            'products' => $products,
            'categories' => $categorieRepository->findAll(),
        ]);
    }

    #[Route('/product/{id}/show', name: 'app_home_product_show', methods: ['GET'])]
    public function show(Product $product, ProductRepository $productRepository, CategorieRepository $categorieRepository): Response
    {
        $lastProductsAdd = $productRepository->findBy([],['id' => 'DESC'], 4);

        return $this->render('home_page/produit.html.twig', [
            'product' => $product,
            'products' => $lastProductsAdd,
            'categories'=> $categorieRepository->findAll(),
        ]);
    }

    #[Route('/product/subcategory/{id}/filter', name: 'app_home_produit_filter', methods: ['GET'])]
    public function filter( $id, SubCategoryRepository $subCategoryRepository, CategorieRepository $categorieRepository): Response
    {
        return $this->render('home_page/filter.html.twig', [
            'products' => $subCategoryRepository->find($id)->getProducts(),
            'subcategory' => $subCategoryRepository->find($id),
            'categories' => $categorieRepository->findAll(),
        ]);
    }
}
