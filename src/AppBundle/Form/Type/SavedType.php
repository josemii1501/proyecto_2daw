<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Saved;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SavedType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('video', null, [
                'label'=> 'Video: ',
                'required'=>true
            ])
            ->add('usuario', null,[
                'label'=>'Usuario: ',
                'required'=>true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Saved::class,
            'new' => false
        ]);
    }

}
