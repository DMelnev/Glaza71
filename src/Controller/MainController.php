<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\MainPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    public function showArticle(Article $article)
    {
        return $this->render('article.html.twig', [
            'article' => $article,
            'folder'=>$this->getParameter('app.upload_path'),
        ]);
    }

//    /**
//     * @Route ("/article/{slug}", name="app_article_show")
//     */
//    public function show(Article $article)
//    {
//        return $this->render('articles/show.html.twig', [
//            "article" => $article,
//        ]);
//    }

}
