<?php

namespace App\Form;

use App\Form\Model\UserRegistrationFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserRegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => '* Enter E-mail address:',
                'required' => false,
                'attr' => [
                    'placeholder' => 'E-mail address'
                ]
            ])
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

            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Пароли должны совпадать.',
                'options' => ['attr' => ['class' => 'password-field']],
                'required' => false,
                'first_options'  => ['label' => '* Enter password:'],
                'second_options' => ['label' => '* Repeat Password:'],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'Consent to the processing of personal data',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => UserRegistrationFormModel::class,
        ]);
    }
}
