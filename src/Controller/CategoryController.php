<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Categorie;
use App\Form\CategoryFormType;
use App\Repository\CategorieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class CategoryController extends AbstractController
{
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategorieRepository $repo, ): Response
    {
        $category= $repo -> findall();
        return $this->render('category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'category' => $category
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $em, Request $request ): Response
    {
        $category = new Categorie();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em-> flush();

            $this->addFlash('success','votre catégorie a eté bien ajouter !!');
             return $this->redirectToRoute('app_category');
        }
            return $this->render('category/newCategory.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/admin/categoryUpdate{id}', name: 'app_category_update')]
    public function updateCategory($id, EntityManagerInterface $em, Request $request, Categorie $category): Response
    {
        
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em-> flush();

            $this->addFlash('success','votre catégorie a été bien modifier !!');
             return $this->redirectToRoute('app_category');
        }
            return $this->render('category/updateCategory.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }
    #[Route('/admin/CategorieDelete/{id}', name: 'app_category_delete')]
    public function deleteForm($id, EntityManagerInterface $entityManager): Response
    {
        $category = $entityManager->getRepository(Categorie::class)->find($id);
        
        if ($category) {
            $entityManager->remove($category);
            $entityManager->flush();

            $this->addFlash('success', 'Suppression réussi !!');
        } else {
            $this->addFlash('danger', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('app_category');
    }
}
