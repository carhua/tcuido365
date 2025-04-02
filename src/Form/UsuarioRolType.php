<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\UsuarioRol;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioRolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('rol')
            ->add('permisos', CollectionType::class, [
                'required' => false,
                'entry_type' => UsuarioPermisoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false, // permite agregar un nuevo en linea
                'entry_options' => [
                    'attr' => ['class' => '_permiso'],
                ],
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UsuarioRol::class,
        ]);
    }
}
