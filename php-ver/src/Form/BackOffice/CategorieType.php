<?php

namespace App\Form\BackOffice;

use App\Entity\Categorie;
use App\Form\BackOffice\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CategorieType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, $this->getConfiguration("Nom", "Nom..."))
            ->add('description',TextareaType::class, $this->getConfiguration("Description", "Description..."))
            ->add('type', ChoiceType::class, [
                'choices'  => [
                    'Produit E-commerce' => '1',
                    'Produit Physique' => '2',
                ],
                'placeholder' => 'Choisissez un type',
                'required' => true,
                 'attr' => [
                    'class' => 'kl-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Categorie::class,
        ]);
    }
}
