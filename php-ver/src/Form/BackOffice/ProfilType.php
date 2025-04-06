<?php

namespace App\Form\BackOffice;

use App\Entity\User;
use App\Form\ApplicationType\BackOffice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class ProfilType extends ApplicationType
{

 /**
     * permet d'avoir la configuration de base d'un champ !
     * 
     * @param string $label
     * @param string $placeholder
     * @return array
     */
    protected function getConfiguration($label, $placeholder)
    {
        return[
           'label' => $label,
            'attr' => [
                'placeholder' => $placeholder
            ]
        ];
    }    
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('nom',TextType::class, $this->getConfiguration("Nom", "Nom..."))
        ->add('prenom',TextType::class, $this->getConfiguration("Prénom(s)", "Prénom(s)..."))
        ->add('email',EmailType::class, $this->getConfiguration("email", "Adresse email..."))
        ->add('tel',TextType::class, $this->getConfiguration("Numéro Fixe", "Téléphone..."))
        ->add('picture',FileType::class,[
            'label' => false,
            'required' => false,
            'mapped' => false
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
