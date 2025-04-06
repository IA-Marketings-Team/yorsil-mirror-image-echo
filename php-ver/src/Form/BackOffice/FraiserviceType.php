<?php

namespace App\Form\BackOffice;

use App\Entity\Fraiservice;
use App\Form\BackOffice\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class FraiserviceType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pourcentage', NumberType::class,[
                'label'=>"Pourcentage",
                'scale' => 2, // Nombre de décimales
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('pourcentage_boutique', NumberType::class,[
                'label'=>"Pourcentage boutique",
                'scale' => 2, // Nombre de décimales
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fraiservice::class,
        ]);
    }
}
