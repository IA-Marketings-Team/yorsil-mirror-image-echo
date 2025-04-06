<?php

namespace App\Form\BackOffice;

use App\Entity\ProduitPhysique;
use App\Entity\Operateur;
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

class ProduitPhysiqueType extends ApplicationType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'label' => "Nom du produit viruel",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description',TextareaType::class, [
                'label' => "Description du produit",
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => false,
            ])
            ->add('instruction',TextareaType::class, [
                'label' => "Instruction du produit",
                'attr' => [
                    'class' => 'form-control'
                ],
                'required' => true,
            ])
            ->add('prixAchat', NumberType::class,[
                'label'=>"Prix d'achat",
                'scale' => 2, // Nombre de décimales
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prixVente', NumberType::class,[
                'label'=>"Prix de vente",
                'scale' => 2, // Nombre de décimales
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
                        'class' => 'kl-select'
                    ]
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Catalogue',
                'choices'  => [
                    'Carte Recharges Mobiles' => '1',
                    'Jeux' => '2',
                    'Paiement' => '3',
                    'Carte Cadeaux' => '4',
                ],
                'placeholder' => 'Choisissez un catalogue',
                'required' => false,
                 'attr' => [
                    'class' => 'kl-select'
                ]
            ])
            ->add('gencode',TextType::class, [
                'label' => "GenCode",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'placeholder' => "Choisissez un catalogue",
                'choice_label' => 'nom',
                'label' => false,
                'required' => false,
                'attr' => [
                        'class' => 'kl-select'
                    ],
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where('c.type = 2') // Exemple de condition WHERE
                        ->orderBy('c.nom', 'ASC'); // Trie par nom
                }
            ]);

    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => ProduitPhysique::class
        ]);
    }
}
