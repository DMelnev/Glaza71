<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentFormType extends AbstractType
{
    private UserRepository $userRepository;
    private ArticleRepository $articleRepository;

    public function __construct(UserRepository $userRepository, ArticleRepository $articleRepository)
    {
        $this->userRepository = $userRepository;
        $this->articleRepository = $articleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('text', TextareaType::class, [
                'label' => 'Comment',
                'rows' => '10',
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label'=>"Comment's data publication",
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('article', EntityType::class, [
                'label'=>"Article",
                'class' => Article::class,
                'choice_label' => function (Article $article) {
                    return sprintf('%d - %s', $article->getId(), $article->getTitle());
                },
                'placeholder' => 'Select article',
                'choices' => $this->articleRepository->findAllSortedByUpdate(),
                'invalid_message' => "Статья не найдена",
            ])
            ->add('author', EntityType::class, [
                'label'=>"Author",
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%d - %s %s', $user->getId(), $user->getFirstName(), $user->getSurname());
                },
                'placeholder' => 'Select author',
                'choices' => $this->userRepository->findAllSortedByName(),
                'invalid_message' => "Пользователь не найден",
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
