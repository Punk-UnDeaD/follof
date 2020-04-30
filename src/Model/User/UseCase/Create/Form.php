<?php

namespace App\Model\User\UseCase\Create;

use App\Model\User\Entity\User\Role;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Form extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', Type\TextType::class, ['label' => 'First Name'])
            ->add('lastName', Type\TextType::class, ['label' => 'Last Name'])
            ->add('email', Type\EmailType::class, ['label' => 'Email'])
            ->add('isNotify', Type\CheckboxType::class, ['label' => 'Notify', 'required' => false])
            ->add(
                'role',
                Type\ChoiceType::class,
                [
                    'label' => 'Role',
                    'choices' => [
                        'User' => Role::USER,
                        'Manager' => Role::MANAGER,
                        'Admin' => Role::ADMIN,
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Command::class,
            ]
        );
    }
}
