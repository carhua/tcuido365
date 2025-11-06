<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form;

use App\Entity\CentroPoblado;
use App\Entity\Distrito;
use App\Entity\Provincia;
use App\Entity\Usuario;
use App\Entity\Institucion;
use App\Entity\UsuarioRol;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class UsuarioType extends AbstractType
{
    public function __construct(
        private readonly EntityManagerInterface $em
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fullName', TextType::class, [
                'required' => true,
                'popover' => 'Nombre completo del usuario',
                'label' => 'Nombres y Apellidos',
                'attr' => ['placeholder' => 'Ej. Juan Perez, Manuel Castillo, etc.'],
            ])
            ->add('username', TextType::class, [
                'required' => true,
                'label' => 'Usuario del Sistema',
                'attr' => ['placeholder' => 'Ej. jperez, mcastillo, etc.'],
                'popover' => 'Ingrese el nombre de usuario sin espacios ni carácteres especiales',
            ])
            ->add('email', EmailType::class, [
                'required' => false,
            ])
            ->add('telefono', TextType::class, [
                'required' => false,
                // 'popover' => '',
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Las contraseñas no coinciden',
                'required' => true,
                'first_options' => ['label' => 'Contraseña'],
                'second_options' => ['label' => 'Repetir contraseña'],
                'popover' => 'La contraseña debe ser minimo 8 caracteres, debe conterner al menos una letra minuscula,mayuscula,un numero',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Por favor, introduzca una contraseña.',
                    ]),
                ],
            ])
            ->add('usuarioRoles', EntityType::class, [
                'class' => UsuarioRol::class,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('r')
                        ->where('r.isActive = TRUE')
                        ->orderBy('r.nombre', 'ASC');
                },
                'popover' => 'Seleccione el rol especifico de este usuario',
            ]);

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $this->onPreSetData($event);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $this->onPreSubmit($event);
        });
    }

    protected function addElements(FormInterface $form, Provincia $provincia = null, Distrito $distrito = null, Institucion $institucion = null): void
    {
        $form->add('provincia', EntityType::class, [
            'required' => true,
            'data' => $provincia,
            'placeholder' => 'Seleccionar',
            'attr' => [
                'class' => 'class_select_provincia',
            ],
            'class' => Provincia::class,
            'constraints' => [
                new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                    if ($this->isAutoridadComunitaria($context) && null === $value) {
                        $context->buildViolation('Este campo es obligatorio para la Autoridad Comunitaria.')->addViolation();
                    }
                    if ($this->isAutoridadComunitaria($context) && null !== $value && method_exists($value, 'getNombre') && 'TODOS' === $value->getNombre()) {
                        $context->buildViolation('La Autoridad Comunitaria no puede seleccionar "TODOS" como Provincia.')->addViolation();
                    }

                    if ($this->isOperadorProteccion($context) && null === $value) {
                        $context->buildViolation('Este campo es obligatorio para el Operador de Protección.')->addViolation();
                    }
                }),
            ],
        ]);

        $form->add('distrito', EntityType::class, [
            'required' => true,
            'data' => $distrito,
            'class' => Distrito::class,
            'placeholder' => 'Seleccionar',
            'attr' => [
                'class' => 'class_select_distrito',
            ],
            'query_builder' => function (EntityRepository $er) use ($provincia) {
                if ($provincia instanceof Provincia) {
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
                       // ->orWhere('r.nombre = :val2')
                       // ->setParameter('val2','TODOS')
                        ->setParameter('provinciaId', null)
                        ->orderBy('r.nombre', 'ASC');
            },
            'constraints' => [
                new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                    if ($this->isAutoridadComunitaria($context) && null === $value) {
                        $context->buildViolation('Este campo es obligatorio para la Autoridad Comunitaria.')->addViolation();
                    }
                    if ($this->isAutoridadComunitaria($context) && null !== $value && method_exists($value, 'getNombre') && 'TODOS' === $value->getNombre()) {
                        $context->buildViolation('La Autoridad Comunitaria no puede seleccionar "TODOS" como Distrito.')->addViolation();
                    }

                    if ($this->isOperadorProteccion($context) && null === $value) {
                        $context->buildViolation('Este campo es obligatorio para el Operador de Protección.')->addViolation();
                    }
                }),
            ],
        ]);

        $form->add('centroPoblado', EntityType::class, [
            'required' => true,
            'class' => CentroPoblado::class,
            'placeholder' => 'Seleccionar',
            'query_builder' => function (EntityRepository $er) use ($distrito) {
                if ($distrito instanceof Distrito) {
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
            'constraints' => [
                new Assert\Callback(function ($value, ExecutionContextInterface $context) {
                    if ($this->isAutoridadComunitaria($context) && null === $value) {
                        $context->buildViolation('Este campo es obligatorio para la Autoridad Comunitaria.')->addViolation();
                    }

                    if ($this->isAutoridadComunitaria($context) && null !== $value && method_exists($value, 'getNombre') && 'TODOS' === $value->getNombre()) {
                        $context->buildViolation('La Autoridad Comunitaria no puede seleccionar "TODOS" como Centro Poblado.')->addViolation();
                    }

                    if ($this->isOperadorProteccion($context) && (null !== $value && (method_exists($value, 'getNombre') && 'TODOS' !== $value->getNombre()))) {
                        $context->buildViolation('El Operador de Protección solo puede seleccionar "TODOS".')->addViolation();
                    }
                }),
            ],
        ]);

        $form->add('institucion', EntityType::class, [
            'required' => true,
            'data' => $institucion,
            'placeholder' => 'Seleccionar',
            'attr' => [
                'class' => 'class_select_institucion',
            ],
            'class' => Institucion::class,
        ]);
    }

    public function onPreSubmit(FormEvent $event): void
    {
        $form = $event->getForm();
        $data = $event->getData();

        $provincia = $this->em->getRepository(Provincia::class)->find($data['provincia']);
        $distrito = $this->em->getRepository(Distrito::class)->find($data['distrito']);
        $institucion = $this->em->getRepository(Institucion::class)->find($data['institucion']);
        $this->addElements($form, $provincia, $distrito, $institucion);
    }

    public function onPreSetData(FormEvent $event): void
    {
        $usuario = $event->getData();
        $form = $event->getForm();

        $provincia = $usuario->getProvincia() ?: null;
        $distrito = $usuario->getDistrito() ?: null;
        $institucion = $usuario->getInstitucion() ?: null;

        $this->addElements($form, $provincia, $distrito, $institucion);
    }

    private function getRolesFromContext(ExecutionContextInterface $context): array
    {
        $form = $context->getRoot();
        $data = $form->getData();

        if ($data instanceof Usuario) {
            return $data->getUsuarioRoles();
        }

        return [];
    }

    private function isAutoridadComunitaria(ExecutionContextInterface $context): bool
    {
        $roles = $this->getRolesFromContext($context);
        foreach ($roles as $rol) {
            if ('ROLE_AUTORIDADCOMUN' === $rol->getRol()) {
                return true;
            }
        }

        return false;
    }

    private function isOperadorProteccion(ExecutionContextInterface $context): bool
    {
        $roles = $this->getRolesFromContext($context);
        foreach ($roles as $rol) {
            if ('ROLE_OPERADORPROTECCION' === $rol->getRol()) {
                return true;
            }
        }

        return false;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Usuario::class,
        ]);
    }
}
