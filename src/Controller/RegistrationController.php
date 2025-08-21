<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, Security $security, EntityManagerInterface $entityManager): Response
    {   // Symfony injecte automatiquement les services nécessaires :
        // Request : pour récupérer les données soumises par le formulaire
        // UserPasswordHasherInterface : pour sécuriser le mot de passe
        // Security : pour gérer la connexion automatique
        // EntityManagerInterface : pour sauvegarder l’utilisateur en base de données

        $user = new User(); //On crée une nouvelle entité User
        $form = $this->createForm(RegistrationFormType::class, $user); // On construit un formulaire basé sur RegistrationFormType, relié à cet objet.
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) { //Vérifie si le formulaire a bien été soumis et s’il est valide.
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData(); //Récupère le mot de passe en clair entré par l’utilisateur (champ plainPassword).

            // On définit ce mot de passe sécurisé dans l’utilisateur.
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword)); //Le mot de passe en clair est haché (encrypté façon sécurisée) via UserPasswordHasherInterface.

            $entityManager->persist($user); //On demande à Doctrine de préparer l’enregistrement de l’utilisateur (persist) puis d’exécuter l’écriture en BDD (flush).
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $security->login($user, 'form_login', 'main'); // pour connecter automatiquement le nouvel utilisateur avec le firewall main et le form_login authenticator.
        }

        return $this->render('registration/register.html.twig', [ //Si le formulaire n’est pas encore soumis ou est invalide, on affiche la vue registration/register.html.twig.
            'registrationForm' => $form, // permet d’afficher le formulaire.
        ]);
    }
}


// En résumé:
// Ce contrôleur gère la création d’un compte utilisateur :
// - Affiche un formulaire d’inscription.
// - Valide et enregistre l’utilisateur en base.
// - Hash son mot de passe de manière sécurisée.
// - Connecte automatiquement l’utilisateur une fois inscrit.