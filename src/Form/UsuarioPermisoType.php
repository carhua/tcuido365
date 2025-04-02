<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\Menu;
use App\Entity\UsuarioPermiso;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsuarioPermisoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('menu', EntityType::class, [
                'class' => Menu::class,
                'required' => false,
                'placeholder' => 'Seleccione ...',
                'group_by' => 'padre',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('m')
                        // ->leftJoin('m.padre','p')
                        ->where('m.isActive = :activo')
                        ->andWhere('m.padre IS NOT NULL')
                        ->setParameter('activo', true)
                        ->orderBy('m.orden', 'ASC');
                },
            ])
            ->add('listar')
            ->add('mostrar')
            ->add('crear')
            ->add('editar')
            ->add('eliminar')
            ->add('imprimir')
            ->add('exportar')
            ->add('maestro')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UsuarioPermiso::class,
        ]);
    }
}
