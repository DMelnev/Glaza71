<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Feedback;
use App\Entity\User;
use App\Form\CommentForUserFormType;
use App\Form\FeedbackForUserFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\FeedbackRepository;
use App\Repository\MainPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="app_index")
     */
    public function index(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $pageNumber = 1;
        $form = $this->formHandler($request, $feedbackRepository, $entityManager, $pageNumber);

        return $this->renderForm('main/index.html.twig',
            array_merge(
                $this->mainPage(
                    $mainPageRepository,
                    $articleRepository,
                    $commentRepository,
                    $feedbackRepository,
                    $pageNumber),
                [
                    'form' => $form,
                ]
            ));
    }

    /**
     * @Route("/optic", name="app_optic")
     */
    public function optic(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $pageNumber = 2;
        $form = $this->formHandler($request, $feedbackRepository, $entityManager, $pageNumber);
        return $this->renderForm('main/index.html.twig',
            array_merge(
                $this->mainPage(
                    $mainPageRepository,
                    $articleRepository,
                    $commentRepository,
                    $feedbackRepository,
                    $pageNumber),
                [
                    'form' => $form,
                ]
            ));
    }

    /**
     * @Route("/tomography", name="app_tomography")
     */
    public function tomography(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response
    {
        $pageNumber = 3;
        $form = $this->formHandler($request, $feedbackRepository, $entityManager, $pageNumber);
        return $this->renderForm('main/index.html.twig',
            array_merge(
                $this->mainPage(
                    $mainPageRepository,
                    $articleRepository,
                    $commentRepository,
                    $feedbackRepository,
                    $pageNumber),
                [
                    'form' => $form,
                ]
            ));
    }

    /**
     * @Route("/contacts", name="app_contacts")
     */
    public function contacts(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository
    ): Response
    {
        $pageNumber = 4;
        return $this->renderForm('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                $feedbackRepository,
                $pageNumber));
    }

    /**
     * @Route("/price", name="app_price")
     */
    public function price(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository
    ): Response
    {
        $pageNumber = 5;
        return $this->renderForm('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                $feedbackRepository,
                $pageNumber));
    }

    /**
     * @Route("/certificates", name="app_certificates")
     */
    public function certificates(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository
    ): Response
    {
        $pageNumber = 6;
        return $this->renderForm('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                $feedbackRepository,
                $pageNumber));
    }

    private function mainPage(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
        FeedbackRepository $feedbackRepository,
        int $index
    ): array
    {

        $page = $mainPageRepository->find($index);
        $articles = null;
        if ($page->getShowArticles()) {
            $articles = $articleRepository->findLast((int)$page->getShowArticles());
        }
        $comments = null;
        if ($page->getShowComments()) {
            $comments = $commentRepository->findLast((int)$page->getShowComments());
        }
        /** @var User $user */
        $user = $this->getUser();
        $feedbacks = $feedbackRepository->findAllCurrentPage($index, $user ? $user->getId() : 0);

        return [
            'page' => $page,
            'articles' => $articles,
            'comments' => $comments,
            'feedbacks' => $feedbacks,
        ];
    }

    private function formHandler(
        Request $request,
        FeedbackRepository $feedbackRepository,
        EntityManagerInterface $entityManager,
        int $pageNumber
    ): FormInterface
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(FeedbackForUserFormType::class, new Feedback(), [
            'method' => 'POST',
            'action' => '#feedback',
        ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user && in_array('ROLE_REGISTERED', $user->getRoles())) {
                /** @var Feedback $feedback */
                $feedback = $form->getData();
                $feedback->setAuthor($user);

                $lastFeedback = $feedbackRepository->findLastCurrentAuthor($user->getId());
                $lastFeedback = $lastFeedback ? $feedbackRepository->findLastCurrentAuthor($user->getId())[0] : new Comment();

                if ($feedback->getText() == $lastFeedback->getText()) {
                    $this->addFlash('flash_error_comment', "Кажется это уже было 🤓");
                } elseif ($lastFeedback->getCreatedAt() > new \DateTime('-5 min',) && !in_array('ROLE_ADMIN', $user->getRoles())) {
                    $this->addFlash('flash_error_comment', "Слишком частая отправка отзывов");
                } else {
                    $feedback->setMainPage($pageNumber);
                    $entityManager->persist($feedback);
                    $entityManager->flush();
                    $this->addFlash('flash_comment', "Отзыв создан!");
                }
                unset($feedback);
                unset($form);
                $form = $this->createForm(FeedbackForUserFormType::class, new Feedback(), [
                    'method' => 'POST',
                    'action' => '#feedback',
                ]);
            } elseif ($user && in_array('ROLE_BANNED', $user->getRoles())) {
                $this->addFlash('flash_error_comment', "Вам запрещено оставлять отзывы.");
            } elseif ($user && !$user->getConfirmed()) {
                $this->addFlash('flash_error_comment', "Вы не можете оставлять отзывы. e-mail не подтвержден.");
            } else {
                $this->addFlash('flash_error_comment', "Отзывы могут оставлять только зарегистрированные пользователи!");
            }
        }
        return $form;
    }

    /**
     * @Route("/sitemap", name="app_sitemap")
     */
    public function sitemap(
        ArticleRepository $articleRepository,
        MainPageRepository $pageRepository
    ): Response
    {
        $articles = $articleRepository->findAllSortedByUpdate();
        $mainPages = $pageRepository->findAll();
        return $this->render('main/sitemap.html.twig', [
            'pages' => $mainPages,
            'articles' => $articles,
        ]);
    }

    /**
     * @Route("/search", name="app_search")
     */
    public function search(
        ArticleRepository $articleRepository,
        MainPageRepository $pageRepository,
        CommentRepository $commentRepository,
        Request $request
    ): Response
    {
        $search = $request->get('search');
        if (null === $search) $search = '';
        $articles = $articleRepository->search($search);
        $mainPages = $pageRepository->search($search);
        $comments = $commentRepository->search($search);

        return $this->render('main/search.html.twig', [
            'articles' => $articles,
            'pages' => $mainPages,
            'comments' => $comments,
        ]);
    }

    /**
     * @Route ("/article/{slug}", name="app_article_show")
     */
    public function showArticle(
        Article $article,
        CommentRepository $commentRepository,
        Request $request,
        EntityManagerInterface $entityManager)
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(CommentForUserFormType::class, new Comment(), [
            'method' => 'POST',
            'action' => '#comment',
        ]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($user && in_array('ROLE_REGISTERED', $user->getRoles())) {
                /** @var Comment $comment */
                $comment = $form->getData();
                $comment->setAuthor($user);
                $comment->setArticle($article);
//                $comment->setText(strip_tags($comment->getText(), '<br>'));
                $lastComment = $commentRepository->findLastCurrentAuthor($user->getId());
                $lastComment = $lastComment ? $commentRepository->findLastCurrentAuthor($user->getId())[0] : new Comment();

                if ($comment->getText() == $lastComment->getText()) {
                    $this->addFlash('flash_error_comment', "Кажется это уже было 🤓");
                } elseif ($lastComment->getCreatedAt() > new \DateTime('-5 min',) && !in_array('ROLE_ADMIN', $user->getRoles())) {
                    $this->addFlash('flash_error_comment', "Слишком частая отправка комментариев");
                } else {

                    if (in_array('ROLE_ADMIN', $user->getRoles())) {
                        $comment->setPublishedAt(new \DateTime('now'));
                    }
                    $entityManager->persist($comment);
                    $entityManager->flush();
                    $this->addFlash('flash_comment', "Комментарий создан!");
                }
                unset($comment);
                unset($form);
                $form = $this->createForm(CommentForUserFormType::class, new Comment(), [
                    'method' => 'POST',
                    'action' => '#comment',
                ]);
            } elseif ($user && in_array('ROLE_BANNED', $user->getRoles())) {
                $this->addFlash('flash_error_comment', "Вам запрещено оставлять комментарии.");
            } elseif ($user && !$user->getConfirmed()) {
                $this->addFlash('flash_error_comment', "Вы не можете оставлять комментарии. e-mail не подтвержден.");
            } else {
                $this->addFlash('flash_error_comment', "Комментарии могут оставлять только зарегистрированные пользователи!");
            }
        }

        return $this->renderForm('articles/article.html.twig', ['article' => $article,
            'folder' => $this->getParameter('app.upload_path'),
            'comments' => $commentRepository->findByArticleId($article->getId(), $user ? $user->getId() : 0),
            'form' => $form,
        ]);

    }

    /**
     * @Route ("articles/",name="app_articles")
     */
    public function allArticles(ArticleRepository $articleRepository)
    {
        $articles = $articleRepository->findAllSortedByUpdate();
        return $this->render('articles/all_articles.html.twig', [
            'articles' => $articles,
        ]);
    }

}
