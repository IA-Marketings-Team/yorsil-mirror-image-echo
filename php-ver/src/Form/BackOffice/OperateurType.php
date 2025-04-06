<?php

namespace App\Form\BackOffice;

use App\Entity\Operateur;
use App\Entity\Pays;
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

class OperateurType extends ApplicationType
{
   
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'label' => "Nom de l'opérateur",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
             ->add('id_pays', EntityType::class,[
                'class' => Pays::class,
                'choice_label' => 'nom',
                'label'=>'Pays',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('ps')
                        ->orderBy('ps.nom','ASC');
                },
                'attr' => [
                    'class'=>'kl-select'
                ]
            ])
            ->add('type',TextType::class, [
                'label' => 'Famille',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('longueur_code', TextType::class,[
                'label'=>'Longueur de code',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('logo',FileType::class, [
                'label' => false,
                'mapped' => false,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => Operateur::class
        ]);
    }
}
