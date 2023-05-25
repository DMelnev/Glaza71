<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Entity\Feedback;
use App\Entity\MainPage;
use App\Entity\User;
use App\Events\ArticleCreatedEvent;
use App\Form\ArticleFormType;
use App\Form\BannedFormType;
use App\Form\CommentFormType;
use App\Form\FeedbackFormType;
use App\Form\FileUploaderFormType;
use App\Form\MainPageFormType;
use App\Form\Model\BanUserModel;
use App\Repository\ArticleRepository;
use App\Repository\CommentRepository;
use App\Repository\FeedbackRepository;
use App\Repository\UserRepository;
use App\Service\MyFiles;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse;
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

    private ManagerRegistry $doctrine;

    public function __construct(ManagerRegistry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

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
            'fileList' => $files->getListFiles($this->getParameter('app.upload_path')),
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
        $articles = $repository->findAllSortedByUpdateNotPublished();
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

    /**
     * @Route("/admin/comment/{id}/edit", name="app_admin_comment_edit")
     */
    public function editComment(
        Comment $commentRequest,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(CommentFormType::class, $commentRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Comment $comment */
            $comment = $form->getData();
            $entityManager->persist($comment);
            $entityManager->flush();

            $this->addFlash('flash_message', "Комментарий успешно изменен!");
            return $this->redirectToRoute('app_admin_comment_edit', ['id' => $comment->getId()]);
        }
        return $this->renderForm('admin/comment/edit.html.twig', [
            'commentForm' => $form,
            'comment' => $commentRequest,
        ]);
    }

    /**
     * @Route ("/admin/users", name="app_admin_users")
     */
    public function usersList(UserRepository $userRepository, Request $request, PaginatorInterface $paginator)
    {

        $qb = ($request->query->get('banned'))
            ? $userRepository->findBanned()
            : $userRepository->findAllNotBanned();
        $users = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10)
        );

        return $this->render('admin/users_list.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * @Route ("/admin/user/{id}/ban", name="app_admin_user_ban")
     */
    public function banUser(User $user, Request $request): Response
    {
        $banModel = new BanUserModel();
        $banModel
            ->setBanned((bool)$user->getBanned())
            ->setBannedReason($user->getBannedReason());

        $form = $this->createForm(BannedFormType::class, $banModel);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var BanUserModel $data */
            $data = $form->getData();
            if ($data->isBanned()) {
                $user
                    ->setBanned(new \DateTime('now'))
                    ->setBannedReason($data->getBannedReason())
                    ->newRoles(['ROLE_BANNED']);

                $this->addFlash('flash_message', sprintf('Пользователь %s был забанен. Причина: %s',
                    $user->getFirstName() . ' ' . $user->getSurname(),
                    $user->getBannedReason()));

            } else {
                $user
                    ->setBanned(null)
                    ->newRoles(['ROLE_USER']);
                if ($user->getConfirmed()) $user->setRoles(['ROLE_REGISTERED']);
                $this->addFlash('flash_message', sprintf('Пользователь %s был разбанен.',
                    $user->getFirstName() . ' ' . $user->getSurname()));
            }
            $em = $this->doctrine->getManager();
            $em->persist($user);
            $em->flush();
            return $this->redirectToRoute('app_admin_users');
        }
        return $this->renderForm('admin/ban_user.html.twig', [
            'form' => $form,
        ]);
    }

    /**
     * @Route ("/admin/comment/{id}/delete", name="app_admin_comment_delete")
     */
    public function deleteComment(Comment $comment): RedirectResponse
    {
        $em = $this->doctrine->getManager();
        $em->remove($comment);
        $em->flush();
        return $this->redirectToRoute('app_admin_comments');
    }

    /**
     * @Route ("/admin/comment/{id}/allow", name="app_admin_comment_allow")
     */
    public function allowComment(Comment $comment): RedirectResponse
    {
        $comment->setPublishedAt(new \DateTime('now'));
        $em = $this->doctrine->getManager();
        $em->persist($comment);
        $em->flush();
        return $this->redirectToRoute('app_admin_comments');
    }

    /**
     * @Route ("/admin/comment/{id}/disallow", name="app_admin_comment_disallow")
     */
    public function disallowComment(Comment $comment): RedirectResponse
    {
        $comment->setPublishedAt(null);
        $em = $this->doctrine->getManager();
        $em->persist($comment);
        $em->flush();
        return $this->redirectToRoute('app_admin_comments');
    }

    /**
     * @Route ("/admin/feedback/{id}/delete", name="app_admin_feedback_delete")
     */
    public function deleteFeedback(Feedback $feedback): RedirectResponse
    {
        $em = $this->doctrine->getManager();
        $em->remove($feedback);
        $em->flush();
        return $this->redirectToRoute('app_admin_feedbacks');
    }

    /**
     * @Route ("/admin/feedback/{id}/allow", name="app_admin_feedback_allow")
     */
    public function allowFeedback(Feedback $feedback): RedirectResponse
    {
        $feedback->setPublishedAt(new \DateTime('now'));
        $em = $this->doctrine->getManager();
        $em->persist($feedback);
        $em->flush();
        return $this->redirectToRoute('app_admin_feedbacks');
    }

    /**
     * @Route ("/admin/feedback/{id}/disallow", name="app_admin_feedback_disallow")
     */
    public function disallowFeedback(Feedback $feedback): RedirectResponse
    {
        $feedback->setPublishedAt(null);
        $em = $this->doctrine->getManager();
        $em->persist($feedback);
        $em->flush();
        return $this->redirectToRoute('app_admin_feedbacks');
    }

    /**
     * @Route("/admin/feedbacks", name="app_admin_feedbacks")
     */
    public function feedbacksList(
        Request $request,
        FeedbackRepository $feedbackRepository,
        UserRepository $userRepository,
        PaginatorInterface $paginator
    ): Response
    {

        $qb = $feedbackRepository->findWithSearchQuery(
            $request->query->get("q"),
            $request->query->get("user")
        );

        $pagination = $paginator->paginate(
            $qb,
            $request->query->getInt('page', 1),
            $request->query->getInt('limit', 10));

        $users = $userRepository->findAllSortedByName();
        return $this->render('admin/feedback/feedbacks_list.html.twig', [
            'users' => $users,
            'pagination' => $pagination,
        ]);
    }

    /**
     * @Route("/admin/feedback/{id}/edit", name="app_admin_feedback_edit")
     */
    public function editFeedback(
        Feedback $feedbackRequest,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(FeedbackFormType::class, $feedbackRequest);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Comment $feedback */
            $feedback = $form->getData();
            $entityManager->persist($feedback);
            $entityManager->flush();

            $this->addFlash('flash_message', "Отзыв успешно изменен!");
            return $this->redirectToRoute('app_admin_feedback_edit', ['id' => $feedback->getId()]);
        }
        return $this->renderForm('admin/feedback/edit.html.twig', [
            'feedbackForm' => $form,
            'feedback' => $feedbackRequest,
        ]);
    }

}
