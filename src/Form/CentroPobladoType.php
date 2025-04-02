<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CentroPobladoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('codigo', TextType::class, [
                'required' => false,
                'label' => 'Codigo Ubigeo',
            ])
            ->add('nombre')
            ->add('categoria', ChoiceType::class, [
                'choices' => [
                    'Caserio' => 'CASERIO',
                    'Pueblo' => 'PUEBLO',
                    'Villa' => 'VILLA',
                    'Otro' => 'OTRO',
                ],
                'expanded' => false,
                'choice_attr' => function ($category, $key, $value) {
                    return ['class' => 'tipo_'.mb_strtolower($value)];
                },
            ])
            ->add('tipo', ChoiceType::class, [
                'choices' => [
                    'Rural' => 'RURAL',
                    'Urbano' => 'URBANO',
                ],
                'expanded' => false,
                'choice_attr' => function ($category, $key, $value) {
                    return ['class' => 'tipo_'.mb_strtolower($value)];
                },
            ])
            ->add('longitud')
            ->add('latitud')
            ->add('distrito', EntityType::class, [
                'class' => Distrito::class,
                'multiple' => false,
                'placeholder' => 'Seleccione ...',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->orderBy('r.nombre', 'ASC');
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => CentroPoblado::class,
        ]);
    }
}
