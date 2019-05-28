<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Category;
use AppBundle\Entity\User;
use AppBundle\Entity\Video;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
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
            ->add('creator_id',EntityType::class,[
                'label'=>'Creador: ',
                'class'=>User::class,
                'choice_label'=> function(User $user) {
                    return $user->getName() . " " . $user->getLastname();
                },
                'data'=> [$options['user']],
                'mapped'=> false
            ])
            ->add('category_id',EntityType::class,[
                'label'=>'Categoria: ',
                'class'=>Category::class,
                'choice_label'=> function(Category $category) {
                    return $category->getName();
                },
                'data'=> [$options['category']],
                'mapped'=> false
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
            'user' => null,
            'category' => []
        ]);
    }

}
