<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CambioClaveType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['es_admin']) {
            $builder
                ->add('claveActual', PasswordType::class, [
                    'label' => 'Contraseña actual: ',
                    'mapped' => false,
                    'constraints' => [
                        new UserPassword()
                    ]
                ]);
        }
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
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
            'es_admin' => false
        ]);
    }
}
