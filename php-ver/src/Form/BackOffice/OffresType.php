<?php

namespace App\Form\BackOffice;

use App\Entity\Offres;
use App\Entity\Operateur;
use App\Entity\Typeoffres;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class OffresType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'label' => "Nom de l'offre",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
             ->add('description',TextareaType::class, [
                'label' => "Description de l'offre",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('montant', NumberType::class,[
                'label'=>"Montant de l'offre",
                'scale' => 2, // Nombre de décimales
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('devise', TextType::class,[
                'label'=>"Devise",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('operateur', EntityType::class, [
                'class' => Operateur::class,
                'placeholder' => "Sélectionner un opérateur",
                'choice_label' => 'nom',
                'label' => 'Opérateur',
                'attr' => [
                        'class' => 'select2-operateur'
                    ]
            ]);

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => Offres::class
        ]);
    }
}
