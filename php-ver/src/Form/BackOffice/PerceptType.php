<?php

namespace App\Form\BackOffice;

use App\Entity\Percept;
use App\Form\BackOffice\ApplicationType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;

class PerceptType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class, $this->getConfiguration("Nom", "Nom..."))
            ->add('prenom',TextType::class, $this->getConfiguration("Prénom(s)", "Prénom(s)..."))
            ->add('tele',TextType::class, $this->getConfiguration("Numéro", "Téléphone..."))
            ->add('ville', TextType::class,[
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Votre ville...'
                ]
            ])
            ->add('pays', CountryType::class,[
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Votre pays...',
                    'class'=>'kl-select form-control'

                ]
            ])
            // ->add('createur')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Percept::class,
        ]);
    }
}
