<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\Parametro;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ParametroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('padre', EntityType::class, [
                'class' => Parametro::class,
                'required' => false,
                'placeholder' => 'Seleccione ...',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('parametro')
                        // ->leftJoin('estado.padre', 'padre')
                        ->where('parametro.activo = TRUE')
                        ->orderBy('parametro.id', 'DESC');
                },
            ])
            ->add('nombre')
            ->add('alias')
            ->add('valor')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Parametro::class,
        ]);
    }
}
