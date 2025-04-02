<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\Blog;
use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogType extends AbstractType
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'required' => true,
            ])
            ->add('descripcion', TextareaType::class, [
                'required' => true,
            ])
            ->add('adjunto', AdjuntoType::class, [
                'required' => false,
            ])

            ->add('tipo', ChoiceType::class, [
                'choices' => [
                    'Normal' => 'Normal',
                    'Alerta' => 'Alerta',
                ],
                'expanded' => false,
                'choice_attr' => function ($category, $key, $value) {
                    return ['class' => 'tipo_'.mb_strtolower($value)];
                },
            ])
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->onPreSetData($event);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->onPreSubmit($event);
        });
    }

    protected function addElements(FormInterface $form, Provincia $provincia = null, Distrito $distrito = null): void
    {
        $form->add('provincia', EntityType::class, [
            'required' => true,
            'data' => $provincia,
            'placeholder' => 'Seleccionar Provincia',
            'attr' => [
                'class' => 'class_select_provincia',
            ],
            'class' => Provincia::class,
        ]);

        $form->add('distrito', EntityType::class, [
            'required' => true,
            'data' => $distrito,
            'class' => Distrito::class,
            'attr' => [
                'class' => 'class_select_distrito',
            ],
            'query_builder' => function (EntityRepository $er) use ($provincia) {
                if ($provincia instanceof \App\Entity\Provincia) {
                    return $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->andWhere('r.provincia =:provinciaId ')
                        ->orWhere('r.nombre = :val2')
                        ->setParameter('provinciaId', $provincia->getId())
                        ->setParameter('val2', 'TODOS')
                        ->orderBy('r.nombre', 'ASC');
                }

                return $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->andWhere('r.provincia =:provinciaId ')
                        ->setParameter('provinciaId', null)
                        ->orderBy('r.nombre', 'ASC');
            },
        ]);

        $form->add('centroPoblado', EntityType::class, [
            'class' => CentroPoblado::class,
            'multiple' => false,
            'query_builder' => function (EntityRepository $er) use ($distrito) {
                if ($distrito instanceof \App\Entity\Distrito) {
                    return $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->andWhere('r.distrito =:distritoId ')
                        ->orWhere('r.nombre = :val2')
                        ->setParameter('distritoId', $distrito->getId())
                        ->setParameter('val2', 'TODOS')
                        ->orderBy('r.nombre', 'ASC');
                }

                return $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->andWhere('r.distrito =:distritoId ')
                        ->setParameter('distritoId', null)
                        ->orderBy('r.nombre', 'ASC');
            },
        ]);
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();
        $provincia = $this->em->getRepository(Provincia::class)->find($data['provincia']);
        $distrito = $this->em->getRepository(Distrito::class)->find($data['distrito']);
        $this->addElements($form, $provincia, $distrito);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $blog = $event->getData();
        $form = $event->getForm();
        $provincia = $blog->getProvincia() ?: null;
        $distrito = $blog->getDistrito() ?: null;
        $this->addElements($form, $provincia, $distrito);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
