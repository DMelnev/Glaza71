<?php

namespace App\Form;

use App\Entity\MainPage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
                    'maxlength' => 250,
                ]
            ])
            ->add('headTitle', TextareaType::class, [
                'label' => 'Description:',
                'rows' => '2',
                'attr' => [
                    'pattern' => false,
                    'maxlength' => 250,
                ]
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text:',
                'rows' => '30',
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
            ->add('showComments', IntegerType::class, [
                'label' => 'Show comments:',
            ])
            ->add('showArticles', IntegerType::class, [
                'label' => 'Show articles:',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MainPage::class,
        ]);
    }
}
