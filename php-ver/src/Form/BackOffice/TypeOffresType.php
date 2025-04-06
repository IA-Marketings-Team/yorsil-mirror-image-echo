<?php

namespace App\Form\BackOffice;

use App\Entity\TypeOffres;
use App\Entity\Operateur;
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

class TypeOffresType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, [
                'label' => "Type d'offre",
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
             ->add('operateur', EntityType::class,[
                'class' => Operateur::class,
                'choice_label' => 'nom',
                'label'=>'Opérateur',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('op')
                        ->orderBy('op.id','DESC');
                },
                'attr' => [
                    'class'=>'kl-select'
                ]
            ])
         ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class'   => TypeOffres::class
        ]);
    }
}