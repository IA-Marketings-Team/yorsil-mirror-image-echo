<?php

namespace App\Form\BackOffice;

use App\Entity\Bout;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;

class BoutType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('code', TextType::class,[
            //     'label'=> 'Code',
            //     'attr' => [
            //         'placeholder' => 'Code...'
            //         ]
            //     ])
            ->add('nom', TextType::class,[
                'label'=>'Nom',
                'attr' => [
                    'placeholder' => 'Nom...'
                    ]
            ])
            ->add('nRcs',TextType::class,[
                'label'=>'N° RCS',
                'attr' => [
                    'placeholder' => 'Numéro RCS...'
                    ]
            ])
           
            ->add('numMobile', TextType::class,[
                'label'=>'Numéro Mobile',
                'attr' => [
                    // 'pattern' => '^(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$',
                    'placeholder' => 'Numéro mobile...'
                    ]
                ,
                'required'=> true
                ])
            ->add('email', EmailType::class,[
                'label'=>'E-mail',
                'attr'=>[
                    'pattern' => '^[\w.+\-]+@[a-zA-Z\d\-]+\.(com|net|org|fr|io|gov|edu|info|biz|co|uk|de|es|it|nl|ca|us)$',
                    'placeholder'=>'email...'
                ],
                'required'=> false
                ])
            ->add('adresse', TextType::class,[
                'label'=>'Adresse',
                'attr'=>[
                    'placeholder'=>'Adresse...'
                ]
                ])
            ->add('codePost', TextType::class,[
                'label'=>'Code Postal',
                'attr'=>[
                    'placeholder'=>'Code Postal...'
                ]
            ])
            ->add('ville', TextType::class,[
                'label'=>'Ville',
                'attr'=>[
                    'placeholder'=>'Ville...'
                ]
            ])
            ->add('pays', CountryType::class,[
                'label' => 'Pays',
                'attr' => [
                    'placeholder' => 'Votre pays...',
                    'class'=>'chosen-select kl-select form-control'

                ]
            ])
            ->add('preuveCabis',FileType::class,[
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*,.pdf'
                ]
            ])
            ->add('pieceIdentity',FileType::class,[
                'label' => false,
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'accept' => 'image/*,.pdf'
                ]
            ])
            ->add('cinGerant', TextType::class,[
                'required' => false,
                'label'=>'CIN du Gérant',
                'attr'=>[
                    'placeholder'=>'CIN Gérant...'
                ]
            ])
            ->add('facturation', ChoiceType::class, [
                'label' => 'Mode de facturation',
                'choices'  => [
                    'Auto-Liquidation' => '1',
                    'TTC' => '2',
                ],
                'placeholder' => 'Choisissez le choix de facturation du point de vente',
                'required' => true,
                 'attr' => [
                    'class' => 'chosen-select kl-select form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bout::class,
        ]);
    }
}
