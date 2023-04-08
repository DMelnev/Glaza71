<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\UserEditFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', null, [
                'label' => '* Enter your name:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Name',
                ]
            ])
            ->add('patronymic', null, [
                'label' => 'Enter your patronymic:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Patronymic'
                ]
            ])
            ->add('surname', null, [
                'label' => '* Enter your surname:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Surname'
                ]
            ])
            ->add('birthDate', DateType::class, [
                'widget' => 'single_text',
//                'widget' => 'choice',
                'label' => 'Enter your birthday:',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserEditFormModel::class,
        ]);
    }
}
