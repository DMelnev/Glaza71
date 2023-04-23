<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\User;
use App\Form\CommentForUserFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\MainPageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        CommentRepository $commentRepository
    ): Response
    {
        return $this->render('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                1));
    }

    /**
     * @Route("/optic", name="app_optic")
     */
    public function optic(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository
    ): Response
    {
        return $this->render('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                2));
    }

    /**
     * @Route("/tomography", name="app_tomography")
     */
    public function tomography(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository
    ): Response
    {
        return $this->render('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                3));
    }

    /**
     * @Route("/contacts", name="app_contacts")
     */
    public function contacts(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository
    ): Response
    {
        return $this->render('main/index.html.twig',
            $this->mainPage(
                $mainPageRepository,
                $articleRepository,
                $commentRepository,
                4));
    }

    private function mainPage(
        MainPageRepository $mainPageRepository,
        ArticleRepository $articleRepository,
        CommentRepository $commentRepository,
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

        return [
            'page' => $page,
            'articles' => $articles,
            'comments' => $comments,
        ];
    }

    /**
     * @Route("/sitemap", name="app_sitemap")
     */
    public function sitemap(): Response
    {
        return $this->render('main/sitemap.html.twig', [

        ]);
    }

    /**
     * @Route("/search", name="app_search")
     */
    public function search(): Response
    {
        return $this->render('main/search.html.twig', [

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

        return $this->renderForm('article.html.twig', ['article' => $article,
            'folder' => $this->getParameter('app.upload_path'),
            'comments' => $commentRepository->findByArticleId($article->getId(), $user ? $user->getId() : 0),
            'form' => $form,
        ]);

    }

}
