<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', null,[
                'label'=>'Nombre: ',
                'required'=>true
            ])
            ->add('photo', FileType::class, [
                'label'=>'Imagen: ',
                'mapped'=>false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'allowPortrait' => false,
                        'allowSquare' => true,
                        'maxSize' => '2M',
                        'minHeight' => 100,
                        'minWidth' => 100
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'new' => false
        ]);
    }

}
