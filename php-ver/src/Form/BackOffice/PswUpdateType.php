<?php

namespace App\Form\BackOffice;

use App\Form\ApplicationType\BackOffice;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PswUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration("Ancien mot de passe", "Donnez votre mot de passe..."))
            ->add('newPassword', PasswordType::class, $this->getConfiguration("Nouveau mot de passe", "Tapez votre nouveau mot de passe..."))
            ->add('comfirmPassword', PasswordType::class, $this->getConfiguration("Confirmation du mot de passe", "Confirmez votre nouveau mot de passe..."))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
