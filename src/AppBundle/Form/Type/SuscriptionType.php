<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Suscription;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SuscriptionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('chanel',null,[
                'label'=> 'Canal: '
            ])
            ->add('suscriptor',null,[
                'label'=>'Suscriptor: '
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Suscription::class
        ]);
    }
}
