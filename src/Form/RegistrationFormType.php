<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder  // Ajout de champs simples (firstName, lastName, email).
            ->add('firstName') // Comme ils existent sûrement dans l’entité User, ils seront mappés automatiquement à l’entité.
            ->add('lastName')
            ->add('email')
            ->add('agreeTerms', CheckboxType::class, [ // Ajout d’une case à cocher pour obliger l’utilisateur à accepter les conditions d’utilisation.
                'mapped' => false, // ce champ ne correspond pas à une propriété de l’entité User. (On ne stocke pas ce champ en base, c’est juste une validation.)
                'constraints' => [
                    new IsTrue([ //contrainte de validation → le champ doit être coché, sinon message d’erreur.
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            ->add('plainPassword', RepeatedType::class, [ // oblige l’utilisateur à taper le mot de passe deux fois (pour éviter les erreurs de saisie).

                'type' => PasswordType::class,
                'invalid_message' => 'ressaye a nouveaux ça ne correspond pas.', // message affiché si les deux champs ne correspondent pas.
                'mapped' => false,
                'attr' => ['autocomplete' => 'new-password'], // dit au navigateur qu’il s’agit d’un nouveau mot de passe (meilleure gestion de la sécurité).
                'required' => true,
                'first_options' => ['label' => 'mot de passe'],
                'second_options' => ['label' => 'confirmer mot de passe'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password', // le mot de passe est obligatoire.
                    ]),
                    new Length([
                        'min' => 6, // minimum de 6 caractères, maximum de 4096 (limite de sécurité de Symfony).
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }
    // Indique que le formulaire est lié à l’entité User.
    // Cela veut dire que Symfony va automatiquement remplir les champs firstName, lastName, email de l’objet User.
    public function configureOptions(OptionsResolver $resolver): void 
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
    
}

// En résumé
// Ce FormType :
// - Déclare les champs nécessaires à l’inscription : prénom, nom, email, case conditions d’utilisation, mot de passe répété.
// - Ajoute des contraintes de validation (mot de passe non vide, longueur minimale, case à cocher obligatoire).
// - Utilise mapped => false pour des champs qui ne vont pas directement dans la base (plainPassword, agreeTerms).
// - Est automatiquement lié à l’entité User, sauf pour les champs exclus.