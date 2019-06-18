<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,[
                'label'=>'Nombre: '
            ])
            ->add('lastname',null,[
                'label'=>'Apellidos: '
            ])
            ->add('email',null,[
                'label'=>'Email: '
            ])
            ->add('phone',null,[
                'label'=>'Teléfono: '
            ])
            ->add('birthday',null,[
                'label'=>'Fecha Nacimiento: ',
                'widget' => 'single_text'
            ])
            ->add('login',null,[
                'label'=>'Usuario: '
            ])
            ->add('avatar',FileType::class,[
                'label'=>'Avatar: ',
                'mapped'=>false,
                'required' => $options['new']
            ])
            ->add('url_web_site',null,[
                'label'=>'URL: '
            ])
            ->add('description',null,[
                'label'=>'Descripción: '
            ]);
            if ($options['es_admin']) {
                $builder            ->add('publisher',null,[
                    'label'=>'¿Publicador? '
                ])
                    ->add('admin',null,[
                        'label'=>'¿Administrador? '
                    ]);
            }
            if($options['new']){
                $builder
                    ->add('nuevaClave', RepeatedType::class, [
                        'type' => PasswordType::class,
                        'mapped' => false,
                        'first_options' => [
                            'label' => 'Nueva contraseña: ',
                            'constraints' => [
                                new Length([
                                    'min' => 6,
                                ]),
                                new NotBlank()
                            ]
                        ],
                        'second_options' => [
                            'label' => 'Repita contraseña',
                        ]
                    ]);
            }

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'new' => false,
            'es_admin' => false
        ]);
    }

}
