<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\Menu;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('padre', EntityType::class, [
                'class' => Menu::class,
                'required' => false,
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('menu')
                        ->where('menu.isActive = true')
                        ->andWhere('menu.padre is null')
                        ->orderBy('menu.nombre', 'ASC');
                },
                'popover' => 'Elegir en que menu estara contenido',
            ])
            ->add('nombre')
            ->add('ruta')
            ->add('icono')
            ->add('orden')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Menu::class,
        ]);
    }
}
