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
{       // final → la classe ne peut pas être héritée (par sécurité/architecture).
    #[Route('/admin/category', name: 'app_category')]
    public function index(CategorieRepository $repo, ): Response
    {
        $category= $repo -> findall(); // Récupère toutes les catégories via le repository
        return $this->render('category/index.html.twig', [ // Affiche la vue Twig category/index.html.twig avec la liste des catégories 
            'controller_name' => 'CategoryController',
            'category' => $category 
        ]);
    }

    #[Route('/admin/category/new', name: 'app_category_new')]
    public function addCategory(EntityManagerInterface $em, Request $request ): Response
    {
        $category = new Categorie();
        $form = $this->createForm(CategoryFormType::class, $category); // Crée une nouvelle catégorie et construit un formulaire à partir du type CategoryFormType
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { // Si le formulaire est soumis et valide : Sauvegarde en base, Affiche un message flash de succès, Redirige vers la liste des catégories.  Sinon, affiche le formulaire de création
            $em->persist($category);
            $em-> flush();

            $this->addFlash('success','votre catégorie a eté bien ajouter !!');
             return $this->redirectToRoute('app_category');
        }
            return $this->render('category/newCategory.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    #[Route('/admin/categoryUpdate{id}', name: 'app_category_update')] // // Symfony injecte directement l’entité Categorie grâce à la param conversion (en fonction de $id)
    public function updateCategory($id, EntityManagerInterface $em, Request $request, Categorie $category): Response
    {
        
        $form = $this->createForm(CategoryFormType::class, $category); // Construit le formulaire d’édition avec les données actuelles
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category); // Sauvegarde les modifications si le formulaire est soumis et valide
            $em-> flush(); // // Sinon, montre le formulaire d’édition

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
        $category = $entityManager->getRepository(Categorie::class)->find($id); // Cherche la catégorie selon l’id donné
        
        if ($category) { // Si trouvée, la supprime définitivement de la base et affiche un message de succès
            $entityManager->remove($category); 
            $entityManager->flush();

            $this->addFlash('success', 'Suppression réussi !!');
        } else {  // Sinon, message d’erreur
            $this->addFlash('danger', 'Utilisateur non trouvé.');
        }

        return $this->redirectToRoute('app_category'); // Redirige vers la liste des catégories après la suppression
    }
}

// Points importants:
    // addFlash() : sert à afficher un message temporaire dans la prochaine page vue par l’utilisateur
    // persist()/flush() : sauvegarde ou modifie l’enregistrement en BDD
    // remove() : supprime l’entité de la base
    // Utilisation des formulaires Symfony pour création et modification
    // Affichage des différentes vues Twig selon l’opération

// En résumé:

    // Ce contrôleur gère la gestion basique CRUD :
    // Lister, créer, modifier, supprimer des catégories
    // Tout passe par les formulaires Symfony et EntityManager pour l’interaction avec la base.






