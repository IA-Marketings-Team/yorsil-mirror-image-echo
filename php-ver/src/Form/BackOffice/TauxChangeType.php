<?php

namespace App\Form\BackOffice;

use App\Entity\TauxChange;
use App\Entity\Pays;
use App\Entity\Categorie;
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

class TauxChangeType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant_change', NumberType::class,[
                'label'=>"Montant du devise",
                'scale' => 2, // Nombre de décimales
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('pays_change', EntityType::class, [
                'class' => Pays::class,
                'placeholder' => "Sélectionner un pays",
                'choice_label' => 'nom',
                'label' => 'Pays',
                'attr' => [
                        'class' => 'kl-select'
                    ]
            ])
            ->add('devise', ChoiceType::class, [
                'label' => 'Devise',
                'choices'  => [
                    'Ariary Malgache(MGA)' => 'MGA', 
                    'Dirham marocain (MAD)' => 'MAD', 
                    'Dollar américain (USD)' => 'USD',
                    'Euro (EUR)' => 'EUR',
                    'Yen japonais (JPY)' => 'JPY',
                    'Livre sterling (GBP)' => 'GBP',
                    'Dollar australien (AUD)' => 'AUD',
                    'Dollar canadien (CAD)' => 'CAD',
                    'Franc suisse (CHF)' => 'CHF',
                    'Yuan chinois (CNY)' => 'CNY',
                    'Couronne suédoise (SEK)' => 'SEK',
                    'Dollar néo-zélandais (NZD)' => 'NZD',
                    'Peso mexicain (MXN)' => 'MXN',
                    'Dollar de Singapour (SGD)' => 'SGD',
                    'Dollar de Hong Kong (HKD)' => 'HKD',
                    'Couronne norvégienne (NOK)' => 'NOK',
                    'Won sud-coréen (KRW)' => 'KRW',
                    'Livre turque (TRY)' => 'TRY',
                    'Roupie indienne (INR)' => 'INR',
                    'Rouble russe (RUB)' => 'RUB',
                    'Réal brésilien (BRL)' => 'BRL',
                    'Rand sud-africain (ZAR)' => 'ZAR'
                ],
                'placeholder' => 'Choisissez une devise',
                'required' => true,
                'attr' => [
                    'class' => 'kl-select'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => TauxChange::class
        ]);
    }

}