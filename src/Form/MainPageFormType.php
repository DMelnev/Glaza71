<?php

namespace App\Form;

use App\Entity\MainPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MainPageFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Title:',
                'attr' => [
                    'pattern' => false,
                    'maxlength' => 60,
                ]
            ])
            ->add('headTitle', null, [
                'label' => 'Description:',
                'attr' => [
                    'pattern' => false,
                    'maxlength' => 250,
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text:',
                'attr' => [
                    'pattern' => false,
                ]
            ])
            ->add('keywords', TextType::class, [
                'label' => 'Keywords:',
                'required' => false,
                'attr' => [
                    'pattern' => false,
                    'maxlength' => 250,
                ]
            ])
            ->add('showComments', ChoiceType::class, [
                'label' => 'Show comments:',
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
            ])
            ->add('showArticles', ChoiceType::class, [
                'label' => 'Show articles:',
                'choices' => [
                    'No' => false,
                    'Yes' => true,
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MainPage::class,
        ]);
    }
}
