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
            ->add('file', FileType::class, [
                'label'=>'Archivo: ',
                'mapped'=>false,
                'required' => true
            ]);
        if ($options['es_admin']) {
            $builder->add('video', null, [
                'label'=> 'Video: '
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => File::class,
            'es_admin'=>false,
            'new' => false
        ]);
    }

}
