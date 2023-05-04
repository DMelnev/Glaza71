<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\Feedback;
use App\Entity\User;
use App\Repository\ArticleRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeedbackFormType extends AbstractType
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
            ->add('mainPage', ChoiceType::class, [
                'label' => 'Page',
//                'placeholder' => 'Select page',
                'choices' => ['главная' => '1', 'оптика' => '2', 'томография' => '3',],
            ])
            ->add('publishedAt', DateTimeType::class, [
                'label' => "Comment's data publication",
                'widget' => 'single_text',
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'label' => "Author",
                'required' => false,
                'class' => User::class,
                'choice_label' => function (User $user) {
                    return sprintf('%d - %s %s', $user->getId(), $user->getFirstName(), $user->getSurname());
                },
                'placeholder' => 'Select author',
                'choices' => $this->userRepository->findAllSortedByName(),
                'invalid_message' => "Пользователь не найден",
            ])
            ->add('otherAuthor', null, [
                'label' => "Other Author",
                'required' => false,
            ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Feedback::class,
        ]);
    }
}
