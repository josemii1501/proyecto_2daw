<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Category;
use AppBundle\Entity\Usuario;
use AppBundle\Entity\Video;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Image;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['es_admin']) {
            $builder
                ->add('creator',null,[
                    'label'=>'Creador: ',
                    'choice_label'=> function(Usuario $usuario) {
                        return $usuario->getName() . " " . $usuario->getLastname();
                    },
                    'mapped'=> true
                ]);
        }

        $builder
            ->add('title',null,[
                'label'=>'Titulo: '
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
                'required'=>false,
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
            'data_class' => Video::class,
            'new' => false,
            'es_admin' => false
        ]);
    }

}
