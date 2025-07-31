<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserUpdateFormType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class UserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_user')]
    public function index(UserRepository $userRepository): Response
    {
        $users= $userRepository->findAll();
        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    #[Route('/admin/user/{id}/update', name: 'app_user_update')]
    public function updateUser(EntityManagerInterface $em, Request $request, User $user): Response
    {
        $isAdmin = $this->isGranted('ROLE_ADMIN'); // Vérifie si l'utilisateur connecté a le rôle ROLE_ADMIN
        $form = $this->createForm(UserUpdateFormType::class, $user, ['allow_roles_update' => $isAdmin ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em-> flush();

            $this->addFlash('success','Utilisateur a été bien modifier !!');
             return $this->redirectToRoute('app_user');
        }
            return $this->render('user/updateUser.html.twig', [
            'form' => $form->createView(),
            
        ]);
    }

    // #[Route('/admin/user/{id}/to/editor', name: 'app_user_to_editor')]
    // public function changeRole(EntityManagerInterface $em, User $user): Response
    // {
    //     $user->setRoles(['ROLE_EDITOR','ROLE_USER']);
    //     $em->flush();

    //     $this->addFlash('success','le rôle de l\'utilisateur a été modifié avec succès !');
    //     return $this->redirectToRoute('app_user');
    // }
}
