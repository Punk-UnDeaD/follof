<?php

namespace App\ReadModel\Billing\Team\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'billingId',
                Type\TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Billing Id',
                        'onchange' => 'this.form.submit()',
                    ],
                ]
            )
            ->add(
                'email',
                Type\TextType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Owner Email',
                        'onchange' => 'this.form.submit()',
                    ],
                ]
            )
            ->add(
                'balanceMin',
                Type\NumberType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Balance min',
                        'onchange' => 'this.form.submit()',
                    ],
                ]
            )
            ->add(
                'balanceMax',
                Type\NumberType::class,
                [
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Balance max',
                        'onchange' => 'this.form.submit()',
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Filter::class,
                'method' => 'GET',
                'csrf_protection' => false,
            ]
        );
    }
}
