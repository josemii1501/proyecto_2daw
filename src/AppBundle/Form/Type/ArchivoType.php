<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\File;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArchivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('video', null, [
                'label'=> 'Video: '
            ])
            ->add('file', FileType::class, [
                'label'=>'Archivo: ',
                'mapped'=>false,
                'required' => $options['new']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => File::class,
            'new' => false
        ]);
    }

}
