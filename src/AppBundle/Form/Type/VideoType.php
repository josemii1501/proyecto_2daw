<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',null,[
                'label'=>'Titulo: '
            ])
            ->add('creator',null,[
                'label'=>'Creador: ',
                'choice_label'=> function(User $user) {
                    return $user->getName() . " " . $user->getLastname();
                },
                'mapped'=> true
            ])
            ->add('category',null,[
                'label'=>'Categoria: ',
                'choice_label'=> function(Category $category) {
                    return $category->getName();
                },
                'mapped'=> true
            ])
            ->add('route',null,[
                'label'=>'Ruta: '
            ])
            ->add('description',null,[
                'label'=>'Descripción: '
            ])
            ->add('miniature',FileType::class,[
                'label'=>'Miniatura: ',
                'mapped'=>false,
                'required' => $options['new']
            ]);


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
            'new' => false,
        ]);
    }

}
