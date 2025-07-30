<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class UserUpdateFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            // ->add('roles')
            // ->add('password')
            ->add('firstName')
            ->add('lastName')
        ;
        if ($options['allow_roles_update'] ?? false){
            $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôles Utilisateur',
                'choices' => [   
                    'Editeur' => 'ROLE_EDITOR',
                    'User' => 'ROLE_USER',
                    // Autres rôles...
                ],
                'multiple' => true,
                'expanded' => true,
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'allow_roles_update' => false, // Par défaut, l'édition des rôles est désactivée
        ]);
    }
}
