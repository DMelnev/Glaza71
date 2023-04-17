<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\ArticleWordsFilter;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotNull;

class ArticleFormType extends AbstractType
{
    private UserRepository $userRepository;
    private ArticleWordsFilter $wordsFilter;
    private array $filter = ['стакан', 'зацени', 'найм', 'ихний'];

    /**
     * ArticleFormType constructor.
     */
    public function __construct(UserRepository $userRepository, ArticleWordsFilter $wordsFilter)
    {
        $this->userRepository = $userRepository;
        $this->wordsFilter = $wordsFilter;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Article|null $article */
        $article = $options['data'] ?? null;
        $imageConstrains = [
            new Image([
                'maxSize' => '2M',
                'minWidth' => 480,
                'minHeight' => 300,
            ])
        ];
        if (!$article || !$article->getImageFilename()) {
            $imageConstrains[] = new NotNull([
                'message' => 'Выберите файл изображения'
            ]);
        }

        $cantEditAuthor = $article && $article->getId() && $article->isPublished();
        $builder
            ->add('image', FileType::class, [
                'mapped' => false,
                'constraints' => $imageConstrains,
            ])
            ->add('title', TextType::class, [
                'label' => 'Title of article',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'rows' => '2', //manual parameter is defined in TextAreaSizeExtension
            ])
            ->add('text', TextareaType::class, [
                'label' => 'Text of article',
                'rows' => '30',
            ])
            ->add('keywords', null, [
                'label' => 'Keywords',
            ])
            ->add('likes', null, [
                'label' => 'Likes',
            ])
            ->add('author', EntityType::class, [
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%d - %s', $user->getId(), $user->getFirstName());
                },
                'placeholder' => 'Select author',
                'choices' => $this->userRepository->findAllSortedByName(),
                'invalid_message' => "Пользователь не найден",
                'disabled' => $cantEditAuthor,
            ]);

        if ($options['enable_published_at']) {
            $builder->add('publishedAt', DateTimeType::class, [
                'widget' => 'single_text',
                'help' => 'some help',
                'required' => false,
            ]);
        }

        $builder->get('text')
            ->addModelTransformer(new CallbackTransformer(
                function ($bodyFromDatabase) {
                    return $bodyFromDatabase;
                },
                function ($bodyFromInput) {
                    return $this->wordsFilter->filter($bodyFromInput, $this->filter);
                }
            ));
        $builder->get('description')
            ->addModelTransformer(new CallbackTransformer(
                function ($descriptionFromDatabase) {
                    return $descriptionFromDatabase;
                },
                function ($descriptionFromInput) {
                    return $this->wordsFilter->filter($descriptionFromInput, $this->filter);
                }
            ));
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
            'enable_published_at' => false,
        ]);
    }
}
