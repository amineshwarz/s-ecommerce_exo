<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'attr' => [
                    'class' => 'form form-control',
                    'value' => 'v'
                ],
            ])
            ->add('lastName', null, [
                'attr' => [
                    'class' => 'form form-control',
                    'value' => 'v'
                ],
            ])  
            ->add('email', null, [
                'attr' => [
                    'class' => 'form form-control',
                    'value' => 'v'
                ],
            ])           
            ->add('phone', null, [
                'attr' => [
                    'class' => 'form form-control',
                    'value' => '0'
                ],
            ])            
            ->add('adresse', null, [
                'attr' => [
                    'class' => 'form form-control',
                    'value' => 'v'
                ],
            ])
            // ->add('createdAt', null, [
            //     'widget' => 'single_text',
            // ])
            ->add('city', EntityType::class, [
                'class' => City::class,
                'choice_label' => 'name',
                'attr' => [
                    'class' => 'form form-control',
                    'value' => 'v'
                ],
            ])
            ->add('payOnDelivery', null,[
                'label'=>'payez à la livraison',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
