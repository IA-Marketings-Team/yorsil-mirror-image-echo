<?php 

namespace App\Form\BackOffice;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExcelUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('excelFile', FileType::class, [
                // 'label' => 'Upload Excel File',
                'label' => false, 
                'required' => true,
                'mapped' => false, // This is not linked to any entity
            ]);
    }
}
