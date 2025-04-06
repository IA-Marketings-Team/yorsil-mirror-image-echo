<?php

namespace App\Form\BackOffice;

use App\Entity\Fraiserviceboutique;
use App\Entity\Bout;
use Doctrine\ORM\EntityRepository;
use App\Form\BackOffice\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class FraiserviceboutiqueType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('boutique', EntityType::class, [
                'class' => Bout::class,
                'placeholder' => "Choisissez une boutique",
                'choice_label' => 'nom',
                'label' => 'Boutique',
                'required' => true,
                'attr' => [
                        'class' => 'kl-select'
                    ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('b')
                        ->orderBy('b.id', 'DESC');
                }
            ])
            ->add('pourcentage', NumberType::class,[
                'label'=>"Pourcentage",
                'scale' => 2, // Nombre de décimales
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Services',
                'choices'  => [
                    'FlixBus'  => '1',
                    'Ding'     => '2',
                    'Reloadly' => '3',
                    'Aleda'    => '4',
                    'DiaspoTransfert'   => '5',
                    'Produit Viruel'  => '6',
                ],
                'placeholder' => 'Choisissez un service',
                'required' => false,
                 'attr' => [
                    'class' => 'kl-select'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Fraiserviceboutique::class,
        ]);
    }
}
