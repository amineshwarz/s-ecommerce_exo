<?php

namespace App\Controller;

use DateTimeImmutable;
use App\Entity\Product;
use App\Form\ProductType;
use App\Entity\AddProductHistory;
use App\Form\AddStockProductFormType;
use App\Repository\AddProductHistoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('editor/product')]
#[IsGranted('ROLE_EDITOR')]
final class ProductController extends AbstractController
{
    #[Route(name: 'app_product_index', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $image->guessExtension();

                // deplacer le fichier dans le dossier configuré via 'photos_directory'
                $image->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                // Set the new filename in the user entity
                $product->setImage($newFilename);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new AddProductHistory();
            $stockHistory->setQuantity($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();
            $this->addFlash('success', 'Produit ajouté avec succès !');
            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
        
    }

    #[Route('/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                // Générer un nom unique pour le fichier
                $newFilename = uniqid() . '.' . $image->guessExtension();

                // deplacer le fichier dans le dossier configuré via 'photos_directory'
                $image->move(
                    $this->getParameter('photos_directory'),
                    $newFilename
                );

                // Set the new filename in the user entity
                $product->setImage($newFilename);
            }
            $entityManager->persist($product);
            $entityManager->flush();

            $stockHistory = new AddProductHistory();
            $stockHistory->setQuantity($product->getStock());
            $stockHistory->setProduct($product);
            $stockHistory->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($stockHistory);
            $entityManager->flush();

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_product_delete')]
    public function delete(Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($product) {
            $entityManager->remove($product);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression réussi !!');
        }

        return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/add/product/{id}', name: 'app_product_stock_add')]
    public function stockAdd( Product $product, EntityManagerInterface $em, Request $request): Response
    {
        $stockAdd= new AddProductHistory();
        $form = $this->createForm(AddStockProductFormType::class, $stockAdd);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if($stockAdd->getQuantity()> 0) {
                $newQuantity = $product->getStock() + $stockAdd->getQuantity();
                $product->setStock($newQuantity);

                $stockAdd->setCreatedAt( new DateTimeImmutable());
                $stockAdd->setProduct($product);
                $em->persist($stockAdd);
                $em-> flush();


                return $this->redirectToRoute('app_product_index');
            }else {
                $this->addFlash('danger', 'La quantité doit être supérieure à 0.');
                return $this->redirectToRoute('app_product_stock_add', ['id' => $product->getId()]);
            }
        }
        return $this->render('product/addStock.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add/product/{id}/stock/history', name: 'app_product_add_history', methods: ['GET'])]
    public function stockHistory($id ,ProductRepository $productRepository, AddProductHistoryRepository $addProductHistoryRepository): Response
    {
        $product = $productRepository->find($id);
        
        $productaddHistory = $addProductHistoryRepository->findBy(['product' => $product], ['id' => 'DESC']);

        // dd($productaddHistory);
        return $this->render('product/addedStockHistory.html.twig', [
            'product' => $product,
            'addProductHistories' => $productaddHistory,
        ]);
    }  
}
