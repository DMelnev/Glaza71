<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\MainPage;
use App\Events\ArticleCreatedEvent;
use App\Form\ArticleFormType;
use App\Form\FileUploaderFormType;
use App\Form\MainPageFormType;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\UserRepository;
use App\Service\FileUploader;
use App\Service\MyFiles;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    /**
     * @Route("/admin", name="app_admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
        ]);
    }

    /**
     * @Route("/admin/main/{id}", name="app_admin_main_pages")
     */
    public function mainPages(MainPage $mainPage, EntityManagerInterface $entityManager, Request $request): Response
    {
        $form = $this->createForm(MainPageFormType::class, $mainPage);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var MainPage $mainPage */
            $mainPage = $form->getData();
            $entityManager->persist($mainPage);
            $entityManager->flush();

            $this->addFlash('flash_message', "Данные успешно изменены!");
            return $this->redirectToRoute('app_admin_main_pages', [
                'id' => $mainPage->getId(),
            ]);
        }

        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось изменить данные!");
        return $this->renderForm('/admin/main_page.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/pictures", name="app_admin_pictures")
     */
    public function pictures(MyFiles $files, Request $request): Response
    {

        $form = $this->createForm(FileUploaderFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $this->addFlash('flash_message', $files->uploadFile($image));
            }
        }
//        if ($form->isSubmitted()) $this->addFlash('flash_error', "Не удалось сохранить файл!");

        return $this->renderForm('admin/pictures.html.twig', [
            'fileList' => $files->getListFiles('./img/'),
            'form' => $form,
        ]);
    }

    /**
     * @Route("/admin/pictures/{filename}/delete", name="app_admin_pictures_delete")
     */
    public function delete($filename, MyFiles $files): Response
    {
//        dd($filename);
        $this->addFlash('flash_error', $files->deleteFile($filename));
        return $this->redirectToRoute('app_admin_pictures');
    }

    /**
     * @Route("/admin/articles/create", name="app_admin_articles_create")
     */
    public function createArticle(Request $request,
                                  EntityManagerInterface $entityManager,
                                  MyFiles $articleFileUploader,
                                  EventDispatcherInterface $dispatcher
    ): Response
    {
        $form = $this->createForm(ArticleFormType::class, new Article());
        if ($article = $this->handleFormRequest($form, $entityManager, $request, $articleFileUploader)) {
            $this->addFlash('flash_message', "Статья успешно создана!");
            $dispatcher->dispatch(new ArticleCreatedEvent($article));
            return $this->redirectToRoute('app_admin_articles');
        }
        return $this->renderForm('admin/article/create.html.twig', [
            'articleForm' => $form,
        ]);
    }

    /**
     * @Route("/admin/article/{id}/edit", name="app_admin_article_edit")
     */
    public function editArticle(
        Article $article,
        Request $request,
        EntityManagerInterface $entityManager,
        MyFiles $articleFileUploader,
        EventDispatcherInterface $dispatcher
    ): Response
    {
        $form = $this->createForm(ArticleFormType::class, $article);
        if ($article = $this->handleFormRequest($form, $entityManager, $request, $articleFileUploader)) {
            $this->addFlash('flash_message', "Статья успешно изменена!");
            $dispatcher->dispatch(new ArticleCreatedEvent($article));
            return $this->redirectToRoute('app_admin_articles');
        }
        return $this->renderForm('admin/article/edit.html.twig', [
            'articleForm' => $form,
        ]);
    }

    private function handleFormRequest(
        FormInterface $form,
        EntityManagerInterface $entityManager,
        Request $request,
        MyFiles $uploader): ?Article
    {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Article $article */
            $article = $form->getData();
            /** @var UploadedFile|null $image */
            $image = $form->get('image')->getData();
            if ($image) {
                $fileName = $uploader->uploadArticleFile($image, $article->getImageFilename());
                $article->setImageFilename($fileName);
            }
            $entityManager->persist($article);
            $entityManager->flush();
            return $article;
        }
        return null;
    }


    /**
     * @Route("/admin/articles", name="app_admin_articles")
     */
    public function articlesList(ArticleRepository $repository): Response
    {
        $articles = $repository->findAllSortedByUpdate();
        return $this->render('admin/article/articles_list.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route("/admin/comments", name="app_admin_comments")
     */
    public function commentsList(
        Request $request,
        CommentRepository $commentRepository,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response
    {

        $qb = $commentRepository->findWithSearchQuery(
            $request->query->get("q"),
            $request->query->get("user")
        );

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10));

        $users = $userRepository->findAllSortedByName();
        return $this->render('admin/comment/comments_list.html.twig', [
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }
}
