<?php

namespace App\Form\BackOffice;

use App\Entity\Bout;
use App\Entity\Percept;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssigneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('percept', EntityType::class,[
                'class' => Percept::class,
                'label' => false, 
                'placeholder' => 'Liste des Percepeteurs...',
                'choice_label' => 'nom',
                'query_builder' => function(EntityRepository $er){
                    return $er->createQueryBuilder('b')
                        ->orderBy('b.nom','ASC');
                },
                'attr' => [
                    'class'=>'chosen-select'
                ]
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bout::class,
        ]);
    }
}
