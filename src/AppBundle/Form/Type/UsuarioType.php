<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',null,['label'=>'Nombre: '])
            ->add('lastname',null,['label'=>'Apellidos: '])
            ->add('email',null,['label'=>'Email: '])
            ->add('phone',null,['label'=>'Teléfono: '])
            ->add('birthday',null,['label'=>'Fecha Nacimiento: ', 'widget' => 'single_text'])
            ->add('login',null,['label'=>'Usuario: '])
            ->add('password',null,['label'=>'Contraseña: '])
            ->add('avatar',FileType::class,['label'=>'Avatar: '])
            ->add('url_web_site',null,['label'=>'URL: '])
            ->add('description',null,['label'=>'Descripción: '])
            ->add('publisher',null,['label'=>'¿Publicador? '])
            ->add('admin',null,['label'=>'¿Administrador? ']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }

}
