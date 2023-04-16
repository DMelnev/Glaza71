<?php


namespace App\Form\TypeExtensions;


use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextAreaSizeExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes(): iterable
    {
        return [TextareaType::class]; //FormType basic for all
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['rows'] = $options['rows'];
//        parent::buildView($view, $form, $options);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
//        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'rows' => 20,
        ]);
    }
}
