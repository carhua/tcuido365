<?php

/*
 * This file is part of the PIDIA
 * (c) Carlos Chininin <cio@pidia.pe>
 */

namespace App\Form\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PopoverMessageExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [FormType::class];
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['popover'] = $options['popover'] ?? '';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['popover' => null]);
    }
}
