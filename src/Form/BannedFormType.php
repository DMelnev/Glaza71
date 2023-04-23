<?php

namespace App\Form;

use App\Entity\User;
use App\Form\Model\BanUserModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BannedFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('banned', ChoiceType::class, array(
                'label'=>"Do you want to ban user?",
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Yes' => true,
                    'No' => false,
                ],
                'data' => false,
            ))
            ->add('bannedReason', TextType::class, [
                'label' => 'Write the reason for the ban',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => BanUserModel::class,
        ]);
    }
}
