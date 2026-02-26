<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\Region;
use App\Service\UbigeoFilterService;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security as CoreSecurity;

class UbigeoFilterType extends AbstractType
{
    private UbigeoFilterService $ubigeoFilter;
    private CoreSecurity $security;

    public function __construct(UbigeoFilterService $ubigeoFilter, CoreSecurity $security)
    {
        $this->ubigeoFilter = $ubigeoFilter;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        if ($user) {
            $this->ubigeoFilter->setUsuario($user);
        }

        $builder
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('r')
                        ->where('r.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('r.nombre', 'ASC');
                    
                    // Si no es super admin, filtrar por región del usuario
                    if ($user && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                        $region = $user->getRegion();
                        if ($region) {
                            $qb->andWhere('r.id = :regionId')
                               ->setParameter('regionId', $region->getId());
                        }
                    }
                    
                    return $qb;
                },
                'choice_label' => 'nombre',
                'required' => false,
                'placeholder' => 'Seleccione',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('provincia', EntityType::class, [
                'class' => Provincia::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('p')
                        ->where('p.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('p.nombre', 'ASC');
                    
                    // Aplicar filtros según el usuario
                    if ($user && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                        $provincias = $this->ubigeoFilter->getProvinciasDisponibles();
                        if (!empty($provincias)) {
                            $ids = array_map(function($p) { return $p->getId(); }, $provincias);
                            $qb->andWhere('p.id IN (:ids)')
                               ->setParameter('ids', $ids);
                        }
                    }
                    
                    return $qb;
                },
                'choice_label' => 'nombre',
                'required' => false,
                'placeholder' => 'Seleccione',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('distrito', EntityType::class, [
                'class' => Distrito::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('d')
                        ->where('d.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('d.nombre', 'ASC');
                    
                    // Aplicar filtros según el usuario
                    if ($user && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                        $distritos = $this->ubigeoFilter->getDistritosDisponibles();
                        if (!empty($distritos)) {
                            $ids = array_map(function($d) { return $d->getId(); }, $distritos);
                            $qb->andWhere('d.id IN (:ids)')
                               ->setParameter('ids', $ids);
                        }
                    }
                    
                    return $qb;
                },
                'choice_label' => 'nombre',
                'required' => false,
                'placeholder' => 'Seleccione',
                'attr' => ['class' => 'form-select'],
            ])
            ->add('centroPoblado', EntityType::class, [
                'class' => CentroPoblado::class,
                'query_builder' => function (EntityRepository $er) use ($user) {
                    $qb = $er->createQueryBuilder('c')
                        ->where('c.isActive = :active')
                        ->setParameter('active', true)
                        ->orderBy('c.nombre', 'ASC');
                    
                    // Aplicar filtros según el usuario
                    if ($user && !$this->security->isGranted('ROLE_SUPER_ADMIN')) {
                        $centros = $this->ubigeoFilter->getCentrosPobladosDisponibles();
                        if (!empty($centros)) {
                            $ids = array_map(function($c) { return $c->getId(); }, $centros);
                            $qb->andWhere('c.id IN (:ids)')
                               ->setParameter('ids', $ids);
                        }
                    }
                    
                    return $qb;
                },
                'choice_label' => 'nombre',
                'required' => false,
                'placeholder' => 'Seleccione',
                'attr' => ['class' => 'form-select'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
