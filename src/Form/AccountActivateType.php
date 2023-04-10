<?php

namespace App\Form;

use App\Form\Model\AccountActivateModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AccountActivateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('activationCode', null, [
                'required'=>false,
                'label' => '* Enter activation code:',
            ])
       ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AccountActivateModel::class,
        ]);
    }
}
