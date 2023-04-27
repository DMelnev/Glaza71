<?php

namespace App\Form;

use App\Form\Model\FileUploaderFormModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class FileUploaderFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FileUploaderFormModel|null $article */
        $article = $options['data'] ?? null;
        $imageConstrains = [
            new Image([
                'maxSize' => '5M',
                'minWidth' => 480,
                'minHeight' => 300,
            ])
        ];
//        if (!$article || !$article->getImageFilename()) {
//            $imageConstrains[] = new NotNull([
//                'message' => 'Выберите файл изображения'
//            ]);
//        }
        $builder
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label'=>false,
                'constraints' => $imageConstrains,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => FileUploaderFormModel::class,
        ]);
    }
}
