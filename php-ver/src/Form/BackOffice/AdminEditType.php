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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class AdminEditType extends ApplicationType
{
   
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
            ])
            ->add('Password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('Confir_pwd', PasswordType::class, [
                'label' => 'Confirmer le mot de passe',
                'required' => false,
                'mapped' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
