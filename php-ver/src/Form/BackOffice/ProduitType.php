<?php

namespace App\Form\BackOffice;

use App\Entity\Produit;
use App\Entity\Categorie;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class ProduitType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'label' => 'Nom du produit',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('description',TextareaType::class, [
                'label' => 'Description du produit',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prixAchat', MoneyType::class,[
                'label'=>'Prix d\'achat',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prixVente', MoneyType::class,[
                'label'=>'Prix de vente',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('categorie', EntityType::class,[
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label'=>'Catégorie',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('cat')
                        ->orderBy('cat.id','DESC');
                },
                'attr' => [
                    'class'=>'kl-select'
                ]
            ])
            ->add('marque',TextType::class, [
                'label' => 'Marque du produit',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('prixPromo', MoneyType::class,[
                'label'=>'Prix promotion',
                'required' => false,
            ])
            ->add('images',FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => Produit::class
        ]);
    }
}
